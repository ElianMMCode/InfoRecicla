<?php

namespace App\Http\Controllers;

use App\Models\PuntoEca;
use App\Models\Usuario;
use App\Models\Publicaciones;
use App\Models\Alertas;
use App\Models\Admin;
use App\Models\GestorECA;
use App\Models\Ciudadano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreEcaRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateEcaRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\CategoriaMaterial;
use App\Models\TipoMaterial;
use App\Models\Material;

class AdminController extends Controller
{
    // actualizar o eliminar punto eca
    public function updateOrDeleteEca(Request $request)
    {
        // eliminar
        if ($request->has('eliminar')) {
            $id = $request->input('eliminar');
            $punto = \App\Models\PuntoEca::findOrFail($id);
            $punto->delete();
            return back()->with('ok', 'Punto ECA eliminado');
        }
        // editar
        if ($request->has('editar')) {
            $id = $request->input('editar');
            $punto = \App\Models\PuntoEca::findOrFail($id);
            $punto->nombre = $request->input('nombre')[$id] ?? $punto->nombre;
            $punto->gestor_id = $request->input('gestor')[$id] ?? $punto->gestor_id;
            $punto->direccion = $request->input('direccion')[$id] ?? $punto->direccion;
            $punto->estado = $request->input('estado')[$id] ?? $punto->estado;
            $punto->save();
            return back()->with('ok', 'Punto ECA actualizado');
        }
        return back();
    }
    // dashboard admin
    public function indexAdmin(Request $request)
    {
        // stats
        $totalUsuarios  = Usuario::count();
        $totalAdmins   = Admin::where('rol', 'Administrador')->count();
        $totalGestores   = Admin::where('rol', 'GestorECA')->count();
        $totalCiudadanos = Admin::where('rol', 'Ciudadano')->count();
        $totalPuntosECAactivos = PuntoEca::where('estado', 'activo')->count();
        $totalPublicaciones = Publicaciones::count();

        // filtros eca
        $puntosECAQuery = PuntoEca::query();
        if ($request->filled('buscar_eca')) {
            $puntosECAQuery->where('nombre', 'like', '%' . $request->input('buscar_eca') . '%')
                ->orWhere('direccion', 'like', '%' . $request->input('buscar_eca') . '%');
        }
        if ($request->filled('estado_eca')) {
            $puntosECAQuery->where('estado', $request->input('estado_eca'));
        }
        if ($request->filled('gestor_eca')) {
            $puntosECAQuery->where('gestor_id', 'like', '%' . $request->input('gestor_eca') . '%');
        }
        $puntosECA = $puntosECAQuery->orderBy('nombre')->paginate(10)->appends($request->except('page'));

        $categorias = CategoriaMaterial::orderBy('nombre');
        if ($request->filled('buscar_categoria')) {
            $categorias->where('nombre', 'like', "%" . $request->input('buscar_categoria') . "%");
        }
        $categorias = $categorias->paginate(10)->appends($request->except('page'));

        $tipos = TipoMaterial::orderBy('nombre');
        if ($request->filled('buscar_tipo')) {
            $tipos->where('nombre', 'like', "%" . $request->input('buscar_tipo') . "%");
        }
        $tipos = $tipos->paginate(10)->appends($request->except('page'));

        $totalMateriales = Material::count();
        $totalCategorias = CategoriaMaterial::count();
        $usuarios = Usuario::count();



        $ecasActivos = PuntoEca::where('estado', 'activo')->count();



        $totalTipos = TipoMaterial::count();


        $ultimosAdmins = Admin::latest('creado')->limit(10)->get();
        $ultimosGestores = Admin::latest('creado')->limit(10)->get();
        $ultimosCiudadanos = Admin::latest('creado')->limit(10)->get();
        $ultimosPuntosECAactivos = PuntoEca::latest('creado')->limit(10)->get();
        $ultimosPublicaciones = Publicaciones::latest('creado')->limit(10)->get();




        $query = Usuario::query();
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%$buscar%")
                    ->orWhere('correo', 'like', "%$buscar%")
                    ->orWhere('apellido', 'like', "%$buscar%")
                    ->orWhere('nombre_usuario', 'like', "%$buscar%")
                ;
            });
        }
        if ($request->filled('rol')) {
            $query->where('rol', $request->input('rol'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }
        $usuariosList = $query->paginate(10)->appends($request->except('page'));
        $materialesQuery = Material::query();
        if ($request->filled('buscar_material')) {
            $buscarMaterial = $request->input('buscar_material');
            $materialesQuery->where('nombre', 'like', "%$buscarMaterial%");
        }
        if ($request->filled('tipo_material')) {
            $materialesQuery->where('tipo_id', $request->input('tipo_material'));
        }
        if ($request->filled('categoria_material')) {
            $materialesQuery->where('categoria_id', $request->input('categoria_material'));
        }
        $materiales = $materialesQuery->paginate(10)->appends($request->except('page'));
        // stock total
        $stockTotal = \App\Models\Inventario::where('activo', true)->sum('stock_actual');

        // recolecciones semana
        $inicioSemana = now()->startOfWeek();
        $finSemana = now()->endOfWeek();
        $recoleccionesSemana = \App\Models\ProgramacionRecoleccion::whereBetween('fecha', [$inicioSemana, $finSemana])->count();

        $dataView = [
            'totalUsuarios'           => $totalUsuarios,
            'totalAdmins'             => $totalAdmins,
            'totalGestores'           => $totalGestores,
            'totalCiudadanos'         => $totalCiudadanos,
            'totalPuntosECAactivos'   => $totalPuntosECAactivos,
            'totalPublicaciones'      => $totalPublicaciones,
            'ultimosAdmins'           => $ultimosAdmins,
            'ultimosGestores'         => $ultimosGestores,
            'ultimosCiudadanos'       => $ultimosCiudadanos,
            'ultimosPuntosECAactivos' => $ultimosPuntosECAactivos,
            'ultimosPublicaciones'    => $ultimosPublicaciones,
            'categorias'              => $categorias,
            'tipos'                   => $tipos,
            'totalMateriales'         => $totalMateriales,
            'totalCategorias'         => $totalCategorias,
            'totalTipos'              => $totalTipos,
            'usuariosList'            => $usuariosList,
            'materiales'              => $materiales,
            'puntosECA'               => $puntosECA,
            'stockTotal'              => $stockTotal,
            'recoleccionesSemana'     => $recoleccionesSemana,
        ];
        return view('Administracion.administrador', $dataView);
    }

    // crear usuario
    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();

        $carga = [
            'id' => (string) Str::uuid(),
            'correo' => $data['correo'],
            'password' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
            'fecha_nacimiento' => $data['fechaNacimiento'] ?? null,
            'avatar_url' => $data['avatar'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'] ?? null,
            'genero' => $data['genero'] ?? null,
            'localidad' => $data['localidad'] ?? null,
            'estado' => 'activo',
            'creado' => now(),
            'actualizado' => now(),
            // tipo
            'tipo' => $data['tipo'] ?? null,
            // 'rol' => $data['tipo'] ?? null
        ];

        DB::transaction(function () use ($carga) {
            Usuario::create($carga);
        });

        return redirect('/registro/exitoso')->with('ok', true);
    }



    public function storeEca(StoreEcaRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();

            $usuario = Usuario::create([
                'id' => $usuarioId,
                // rol
                'rol' => $data['tipo'],
                'tipo' => $data['tipo'] ?? null,
                'correo' => $data['correo'],
                'password' => Hash::make($data['password']),
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
                'tipo_documento' => $data['tipoDocumento'] ?? null,
                'numero_documento' => $data['numeroDocumento'] ?? null,
                'nombre_usuario' => $data['nombrePunto'] ?? null,
                'estado' => 'activo',
                'creado' => now(),
                'actualizado' => now(),
            ]);

            if ($usuario->rol === 'GestorECA') {
                PuntoEca::create([
                    'id' => (string) Str::uuid(),
                    'gestor_id' => $usuarioId,
                    'nombre' => $data['nombrePunto'],
                    'direccion' => $data['direccionPunto'],
                    'telefonoPunto' => $data['telefonoPunto'],
                    'correoPunto' => $data['correoPunto'],
                    'ciudad' => $data['ciudad'],
                    'localidad' => $data['localidadPunto'],
                    'latitud' => $data['latitud'] ?? null,
                    'longitud' => $data['longitud'] ?? null,
                    'nit' => $data['nit'] ?? null,
                    'horario_atencion' => $data['horarioAtencion'] ?? null,
                    'sitio_web' => $data['sitioWeb'] ?? null,
                    'logo_url' => $data['logo'] ?? null,
                    'foto_url' => $data['foto'] ?? null,
                    'mostrar_mapa' => isset($data['mostrarMapa']) ? (int) (bool) $data['mostrarMapa'] : 0,
                    'creado' => now(),
                    'actualizado' => now(),
                ]);
            }
        });

        return redirect('/registro/exitoso')->with('ok', true);
    }


    // show usuario
    public function usuarioShow(Usuario $usuario)
    {
        return view('Administracion.usuario_show', compact('usuario'));
    }
    public function ecaShow(PuntoEca $puntoECA)
    {
        return view('Administracion.eca_show', compact('puntoECA'));
    }



    // edit eca
    public function ecaEdit(PuntoEca $puntoECA)
    {
        return view('Administracion.eca_edit', compact('puntoECA'));
    }




    // update usuarios
    public function updateUsuarios(StoreUsuarioRequest $request)
    {
        $data = $request->validated();

        $carga = [
            'id' => (string) Str::uuid(),
            'correo' => $data['correo'],
            'password' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
            'fecha_nacimiento' => $data['fechaNacimiento'] ?? null,
            'avatar_url' => $data['avatar'] ?? null,
            'nombre_usuario' => $data['nombre_usuario'] ?? null,
            'genero' => $data['genero'] ?? null,
            'localidad' => $data['localidad'] ?? null,
            'estado' => 'activo',
            'creado' => now(),
            'actualizado' => now(),
            // tipo
            'tipo' => $data['tipo'] ?? null,
            // 'rol' => $data['tipo'] ?? null
        ];

        DB::transaction(function () use ($carga) {
            Usuario::update($carga);
        });

        return redirect()->route('admin')->with('success', 'usuario actualizado correctamente');
    }

    public function updateEca(StoreEcaRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();

            $usuario = Usuario::create([
                'id' => $usuarioId,
                // rol
                'rol' => $data['tipo'],
                'tipo' => $data['tipo'] ?? null,
                'correo' => $data['correo'],
                'password' => Hash::make($data['password']),
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'recibe_notificaciones' => isset($data['recibeNotificaciones']) ? (int) (bool) $data['recibeNotificaciones'] : 0,
                'tipo_documento' => $data['tipoDocumento'] ?? null,
                'numero_documento' => $data['numeroDocumento'] ?? null,
                'nombre_usuario' => $data['nombrePunto'] ?? null,
                'estado' => 'activo',
                'creado' => now(),
                'actualizado' => now(),
            ]);

            if ($usuario->rol === 'GestorECA') {
                PuntoEca::update([
                    'id' => (string) Str::uuid(),
                    'gestor_id' => $usuarioId,
                    'nombre' => $data['nombrePunto'],
                    'direccion' => $data['direccionPunto'],
                    'telefonoPunto' => $data['telefonoPunto'],
                    'correoPunto' => $data['correoPunto'],
                    'ciudad' => $data['ciudad'],
                    'localidad' => $data['localidadPunto'],
                    'latitud' => $data['latitud'] ?? null,
                    'longitud' => $data['longitud'] ?? null,
                    'nit' => $data['nit'] ?? null,
                    'horario_atencion' => $data['horarioAtencion'] ?? null,
                    'sitio_web' => $data['sitioWeb'] ?? null,
                    'logo_url' => $data['logo'] ?? null,
                    'foto_url' => $data['foto'] ?? null,
                    'mostrar_mapa' => isset($data['mostrarMapa']) ? (int) (bool) $data['mostrarMapa'] : 0,
                    'creado' => now(),
                    'actualizado' => now(),
                ]);
            }
        });

        return redirect()->route('admin')->with('success', 'ECA actualizado correctamente');
    }





    // destroy usuario
    public function usuariosDestroy(string $id)
    {
        $usuario = Admin::findOrFail($id);
        $usuario->delete();
        return back()->with('ok', 'Usuario eliminado');
    }
    public function ecaDestroy(string $id)
    {
        $punto = PuntoEca::findOrFail($id);
        $punto->delete();
        return back()->with('ok', 'Punto ECA eliminado');
    }


    public function view_admin()
    {
        //return $this->view('/Admin/', 'admin');
        return view('Administracion.administrador');
    }

    // edit usuario
    public function usuarioEdit(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        if ($request->isMethod('post')) {
            $usuario->nombre = $request->input('nombre');
            $usuario->correo = $request->input('correo');
            $usuario->rol = $request->input('rol');
            $usuario->save();
            return redirect()->back()->with('success', 'Usuario actualizado correctamente');
        }
        return view('Administracion.administrador', [
            'usuarioEdit' => $usuario
        ]);
    }
}
