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
    /**
     * Actualiza o elimina un punto ECA desde la tabla editable del panel admin
     */
    public function updateOrDeleteEca(Request $request)
    {
        // Eliminar
        if ($request->has('eliminar')) {
            $id = $request->input('eliminar');
            $punto = \App\Models\PuntoEca::findOrFail($id);
            $punto->delete();
            return back()->with('ok', 'Punto ECA eliminado');
        }
        // Editar
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
    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request)
    {
        // Mostrar Listado de Estadísticas Usuarios, Puntos ECA, Publicaciones

        $totalUsuarios  = Usuario::count();
        $totalAdmins   = Admin::where('rol', 'Administrador')->count();
        $totalGestores   = Admin::where('rol', 'GestorECA')->count();
        $totalCiudadanos = Admin::where('rol', 'Ciudadano')->count();
        $totalPuntosECAactivos = PuntoEca::where('estado', 'activo')->count();
        $totalPublicaciones = Publicaciones::count();

        // Filtros para puntos ECA
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
        // Contador de stock total (suma de stock_actual de todos los inventarios activos)
        $stockTotal = \App\Models\Inventario::where('activo', true)->sum('stock_actual');

        // Contador de recolecciones de la semana actual
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

    /**
     * Show the form for creating a new resource.
     */
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
            'rol' => $data['tipo'],
        ];

        DB::transaction(function () use ($carga) {
            Usuario::create($carga);
        });

        return back()->with('ok', 'Usuario creado');
    }



    public function storeEca(StoreEcaRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $usuarioId = (string) Str::uuid();

            $usuario = Usuario::create([
                'id' => $usuarioId,
                'rol' => $data['tipo'],
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

        return back()->with('ok', 'Gestor/ECA creado');
    }


    /**
     * Display the specified resource.
     */
    public function usuarioShow(Usuario $usuario)
    {
        return view('Administracion.usuario_show', compact('usuario'));
    }
    public function ecaShow(PuntoEca $puntoECA)
    {
        return view('Administracion.eca_show', compact('puntoECA'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Muestra y procesa el formulario de edición de usuario básico
     */
    public function ecaEdit(PuntoEca $puntoECA)
    {
        return view('Administracion.eca_edit', compact('puntoECA'));
    }




    /**
     * Update the specified resource in storage.
     */
    public function updateUsuarios(StoreUsuarioRequest $request, string $id)
    {
        $data = $request->validated();
        $usuario = Usuario::findOrFail($id);
        DB::transaction(function () use ($usuario, $data) {
            $usuario->correo = $data['correo'];
            $usuario->nombre = $data['nombre'];
            $usuario->apellido = $data['apellido'];
            if (!empty($data['password'])) {
                $usuario->password = Hash::make($data['password']);
            }
            $usuario->nombre_usuario = $data['nombre_usuario'] ?? $usuario->nombre_usuario;
            $usuario->genero = $data['genero'] ?? $usuario->genero;
            $usuario->localidad = $data['localidad'] ?? $usuario->localidad;
            $usuario->rol = $data['tipo'] ?? $usuario->rol; // mantener compatibilidad
            $usuario->actualizado = now();
            $usuario->save();
        });
        return back()->with('ok', 'Usuario actualizado');
    }

    public function updateEca(StoreEcaRequest $request, string $id)
    {
        $data = $request->validated();
        $usuario = Usuario::findOrFail($id);
        DB::transaction(function () use ($usuario, $data) {
            $usuario->correo = $data['correo'];
            $usuario->nombre = $data['nombre'];
            $usuario->apellido = $data['apellido'];
            if (!empty($data['password'])) {
                $usuario->password = Hash::make($data['password']);
            }
            $usuario->rol = $data['tipo'] ?? $usuario->rol;
            $usuario->nombre_usuario = $data['nombrePunto'] ?? $usuario->nombre_usuario;
            $usuario->actualizado = now();
            $usuario->save();

            if ($usuario->rol === 'GestorECA') {
                $punto = PuntoEca::where('gestor_id', $usuario->id)->first();
                if ($punto) {
                    $punto->nombre = $data['nombrePunto'] ?? $punto->nombre;
                    $punto->direccion = $data['direccionPunto'] ?? $punto->direccion;
                    $punto->telefonoPunto = $data['telefonoPunto'] ?? $punto->telefonoPunto;
                    $punto->correoPunto = $data['correoPunto'] ?? $punto->correoPunto;
                    $punto->ciudad = $data['ciudad'] ?? $punto->ciudad;
                    $punto->localidad = $data['localidadPunto'] ?? $punto->localidad;
                    $punto->latitud = $data['latitud'] ?? $punto->latitud;
                    $punto->longitud = $data['longitud'] ?? $punto->longitud;
                    $punto->nit = $data['nit'] ?? $punto->nit;
                    $punto->horario_atencion = $data['horarioAtencion'] ?? $punto->horario_atencion;
                    $punto->sitio_web = $data['sitioWeb'] ?? $punto->sitio_web;
                    $punto->logo_url = $data['logo'] ?? $punto->logo_url;
                    $punto->foto_url = $data['foto'] ?? $punto->foto_url;
                    $punto->mostrar_mapa = isset($data['mostrarMapa']) ? (int) (bool) $data['mostrarMapa'] : $punto->mostrar_mapa;
                    $punto->actualizado = now();
                    $punto->save();
                }
            }
        });
        return back()->with('ok', 'ECA actualizado');
    }





    /**
     * Remove the specified resource from storage.
     */
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

    /**
     * Muestra el formulario de edición de usuario básico
     */
    /**
     * Muestra y procesa el formulario de edición de usuario básico
     */
    public function usuarioEdit(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        if ($request->isMethod('post')) {
            $usuario->nombre = $request->input('nombre');
            $usuario->correo = $request->input('correo');
            $usuario->rol = $request->input('rol');
            $usuario->save();
            return back()->with('ok', 'Usuario editado');
        }
        return view('Administracion.administrador', [
            'usuarioEdit' => $usuario
        ]);
    }
}
