<x-app-layout>

  <link rel="stylesheet" href="css/PuntoECA/punto-eca.css">

  <!-- NAVBAR -->
  <x-navbar-layout>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav"
      aria-expanded="false" aria-label="Alternar navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="nav" class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
        <li class="nav-item"><a class="nav-link" href="/publicaciones">Publicaciones</a></li>
        <li class="nav-item"><a class="nav-link" href="/mapa">Mapa ECA</a></li>
        <li class="nav-item"><a class="btn btn-light text-success fw-semibold" href="/">Salir</a></li>
      </ul>
    </div>
  </x-navbar-layout>

  <main class="container my-4">
    <!-- TABS PRINCIPALES -->
    <ul class="nav nav-pills" id="mainTabs" role="tablist">
      <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab"
          data-bs-target="#tab-panel" type="button">Resumen</button></li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-perfil" type="button">
          Perfil
        </button>
      </li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-materiales" type="button">Materiales</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-historial" type="button">Historial</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-calendario" type="button">Calendario</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-proveedores" type="button">Centros/Proveedores</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-conversaciones" type="button">Conversaciones</button></li>
      <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab"
          data-bs-target="#tab-config" type="button">Configuración</button></li>
    </ul>

    <div class="tab-content pt-3">

      <!-- RESUMEN -->
      <section class="tab-pane fade show active" id="tab-panel">
        <div class="row g-3">
          <div class="col-md-3">
            <div class="card card-hover">
              <div class="card-body">
                <div class="text-muted small">Inventario total (kg)</div>
                <div class="h4 mb-0" id="kpiInventario">—</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-hover">
              <div class="card-body">
                <div class="text-muted small">Entradas mes (kg)</div>
                <div class="h4 mb-0" id="kpiEntradasMes">—</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-hover">
              <div class="card-body">
                <div class="text-muted small">Salidas mes (kg)</div>
                <div class="h4 mb-0" id="kpiSalidasMes">—</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card card-hover">
              <div class="card-body">
                <div class="text-muted small">Próximo despacho</div>
                <div class="h6 mb-0" id="kpiProximoDespacho">—</div>
              </div>
            </div>
          </div>
        </div>
        <div class="card card-hover mt-3">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Alertas de capacidad / umbrales</strong>
            <span class="badge badge-soft" id="alertCount">0</span>
          </div>
          <div class="card-body">
            <div id="alertList" class="vstack gap-2 small text-muted">Sin alertas.</div>
          </div>
        </div>
      </section>

      <section class="tab-pane fade" id="tab-perfil">
        <div class="row g-4">
          <!-- ===== SUBMÓDULO: Datos del Encargado ===== -->
          <div class="col-lg-6">
            <div class="card card-hover">
              <div class="card-body">
                <h5 class="mb-3">Encargado</h5>

                <!-- Foto del encargado (opcional) -->
                <div class="d-flex align-items-center gap-3 mb-3">
                  <img id="previewPerfil" src="../images/perfil_default.png" alt="Foto encargado"
                    class="rounded-circle img-thumbnail" style="width:96px;height:96px;object-fit:cover;">
                  <div class="flex-grow-1">
                    <div class="small text-muted">Foto (opcional)</div>
                    <input class="form-control form-control-sm" type="file" id="fotoPerfil" accept="image/*">
                  </div>
                </div>

                <!-- Form datos del encargado -->
                <form id="formEncargado" class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="mgrNombre" placeholder="—">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="mgrTelefono" placeholder="—">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Correo</label>
                    <input type="email" class="form-control" id="mgrEmail" placeholder="—">
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- ===== SUBMÓDULO: Datos del Punto ECA ===== -->
          <div class="col-lg-6">
            <div class="card card-hover">
              <div class="card-body">
                <h5 class="mb-3">Punto ECA</h5>

                <!-- Foto del punto (opcional) -->
                <div class="d-flex align-items-center gap-3 mb-3">
                  <img id="previewPunto" src="../images/eca-default.png" alt="Foto punto" class="img-thumbnail rounded"
                    style="width:140px;height:100px;object-fit:cover;">
                  <div class="flex-grow-1">
                    <div class="small text-muted">Foto (opcional)</div>
                    <input class="form-control form-control-sm" type="file" id="fotoPunto" accept="image/*">
                  </div>
                </div>

                <!-- Form datos del punto -->
                <form id="formPunto" class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Nombre del Punto</label>
                    <input type="text" class="form-control" id="puntoNombre" placeholder="—">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="puntoDireccion" placeholder="—">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="puntoCiudad" placeholder="—">
                  </div>
                  <div class="col-6">
                    <label class="form-label">Localidad</label>
                    <input type="text" class="form-control" id="puntoLocalidad" placeholder="—">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Coordenadas</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="puntoLatLng" placeholder="Latitud, Longitud" readonly>
                      <button class="btn btn-outline-secondary" type="button" id="btnUbicacion">Obtener
                        ubicación</button>
                    </div>
                    <div class="form-text">Usa tu ubicación del navegador para rellenar automáticamente.</div>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Horario</label>
                    <input type="text" class="form-control" id="puntoHorario" placeholder="Lun–Vie 8:00–17:00">
                  </div>
                </form>

                <div class="text-end mt-3">
                  <button class="btn btn-success" id="btnGuardarPerfil">Guardar cambios</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- MATERIALES (UNIFICA INVENTARIO + REGISTRO) -->
      <section class="tab-pane fade" id="tab-materiales">
        <!-- FILTROS + CTA REGISTRO -->
        <div class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select id="fTipo" class="form-select">
              <option value="">Todos</option>
              <option>Plástico</option>
              <option>Papel y cartón</option>
              <option>Vidrio</option>
              <option>Metales</option>
              <option>Orgánicos</option>
              <option>RAEE</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Categoría</label>
            <select id="fCategoria" class="form-select">
              <option value="">Todas</option>
              <option>PET</option>
              <option>PEAD</option>
              <option>Chatarra</option>
              <option>Aluminio</option>
              <option>Botella verde</option>
              <option>Mixto</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Buscar</label>
            <input id="fTexto" type="search" placeholder="Nombre o descripción…" class="form-control">
          </div>
          <div class="col-md-2 d-grid">
            <button id="btnNuevoMat" class="btn btn-success" data-bs-toggle="modal"
              data-bs-target="#modalRegistrarMaterial">Registrar material</button>
          </div>
        </div>

        <!-- GRID DE CARDS -->
        <div id="gridMateriales" class="row g-3 mt-1"></div>

        <!-- MODAL: REGISTRO DE MATERIAL (único método de alta) -->
        <div class="modal fade" id="modalRegistrarMaterial" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <form class="modal-content" id="formMaterial">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-6"><label class="form-label">Nombre</label><input id="matNombre"
                      class="form-control" required></div>
                  <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select id="matTipo" class="form-select" required>
                      <option>Plástico</option>
                      <option>Papel y cartón</option>
                      <option>Vidrio</option>
                      <option>Metales</option>
                      <option>Orgánicos</option>
                      <option>RAEE</option>
                    </select>
                  </div>
                  <div class="col-md-3"><label class="form-label">Categoría</label><input id="matCategoria"
                      class="form-control" placeholder="p.ej. PET"></div>
                  <div class="col-12"><label class="form-label">Descripción</label><textarea id="matDesc"
                      class="form-control" rows="2" placeholder="Notas del material"></textarea></div>
                  <div class="col-md-4"><label class="form-label">URL de imagen (opcional)</label><input id="matImg"
                      class="form-control" placeholder="https://…"></div>
                  <div class="col-md-4"><label class="form-label">Capacidad Máx. (kg)</label><input id="matCapMax"
                      type="number" min="1" step="0.01" class="form-control" required></div>
                  <div class="col-md-4"><label class="form-label">Stock inicial (kg)</label><input id="matStock"
                      type="number" min="0" step="0.01" class="form-control" value="0"></div>

                  <!-- VISIBILIDAD de capacidad + umbrales dentro del registro -->
                  <div class="col-12">
                    <div class="p-3 border rounded">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small text-muted">Ocupación estimada</span>
                        <span id="matOcupacionPct" class="small fw-semibold">0%</span>
                      </div>
                      <div class="progress compact">
                        <div id="matOcupacionBar" class="progress-bar" style="width:0%"></div>
                      </div>
                      <div class="row g-2 mt-2">
                        <div class="col-md-6"><label class="form-label">Umbral alerta (kg)</label><input
                            id="matUmbralKg" type="number" min="0" step="0.01" class="form-control"
                            placeholder="p.ej. 1000"></div>
                        <div class="col-md-6"><label class="form-label">Umbral crítico (kg)</label><input
                            id="matUmbralCrit" type="number" min="0" step="0.01" class="form-control"
                            placeholder="p.ej. 1200"></div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6"><label class="form-label">Precio compra ($/kg)</label><input
                      id="matPrecioCompra" type="number" min="0" step="0.01" class="form-control" value="0"></div>
                  <div class="col-md-6"><label class="form-label">Precio venta ($/kg)</label><input id="matPrecioVenta"
                      type="number" min="0" step="0.01" class="form-control" value="0"></div>
                </div>

                <hr class="my-3">

                <!-- DESPACHO OPCIONAL -->
                <h6 class="mb-2">Despacho (opcional)</h6>
                <div class="row g-3 align-items-end">
                  <div class="col-md-4">
                    <label class="form-label">Frecuencia</label>
                    <select id="matFreq" class="form-select">
                      <option value="manual">Manual (según necesidad)</option>
                      <option value="semanal">Semanal</option>
                      <option value="quincenal">Cada 15 días</option>
                      <option value="mensual">Mensual</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Día (1-31 o 1-7)</label>
                    <input id="matDia" type="number" min="1" max="31" class="form-control" placeholder="p.ej. 5">
                    <div class="form-text">(1=Lunes … 7=Domingo para semanal)</div>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Hora</label>
                    <input id="matHora" type="time" class="form-control" value="10:00">
                  </div>
                  <div class="col-md-8">
                    <label class="form-label">Centro de acopio</label>
                    <select id="matCentro" class="form-select"></select>
                  </div>
                  <div class="col-md-4 d-grid">
                    <button class="btn btn-outline-success" id="matPlanificarBtn" type="button">Plan preliminar</button>
                  </div>
                </div>
                <div class="form-text mt-2">Si defines frecuencia y centro, se crearán eventos futuros en el calendario
                  del material.</div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                <button class="btn btn-success" type="submit">Guardar material</button>
              </div>
            </form>
          </div>
        </div>

        <!-- MODAL: Detalle del material (visualización/ampliación) -->
        <div class="modal fade" id="modalMaterial" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
              <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><span id="mmNombre">Material</span> <span class="badge badge-soft ms-2"
                    id="mmTipo">—</span> <span class="badge badge-soft ms-1" id="mmCategoria">—</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row g-4">
                  <div class="col-lg-4">
                    <img id="mmImg" class="img-fluid rounded border" src="" alt="Imagen material">
                    <div class="small text-muted mt-2">Almacenamiento: <span id="mmAlmacen"></span></div>
                    <div class="small text-muted">Capacidad (kg): <span id="mmCap"></span></div>
                    <div class="small text-muted">Umbral alerta (kg): <span id="mmUmbral"></span></div>
                    <div class="small text-muted">Umbral crítico (kg): <span id="mmUmbralCrit"></span></div>
                  </div>
                  <div class="col-lg-8">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab"
                          data-bs-target="#mm-info" type="button">Información</button></li>
                      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#mm-despacho"
                          type="button">Despacho / Frecuencia</button></li>
                      <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#mm-historial"
                          type="button">Historial</button></li>
                    </ul>
                    <div class="tab-content border border-top-0 rounded-bottom p-3">
                      <div class="tab-pane fade show active" id="mm-info">
                        <p id="mmDesc" class="mb-2">—</p>
                        <div class="row g-3">
                          <div class="col-md-6"><label class="form-label">Precio compra (x kg)</label><input
                              id="mmPrecioCompra" type="number" class="form-control" min="0" step="0.01"></div>
                          <div class="col-md-6"><label class="form-label">Precio venta (x kg)</label><input
                              id="mmPrecioVenta" type="number" class="form-control" min="0" step="0.01"></div>
                          <div class="col-12 text-end"><button class="btn btn-success btn-sm" id="mmGuardarInfo">Guardar
                              cambios</button></div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="mm-despacho">
                        <div class="row g-3 align-items-end">
                          <div class="col-md-6"><label class="form-label">Frecuencia de despacho</label>
                            <select id="mmFreq" class="form-select">
                              <option value="manual">Manual (según necesidad)</option>
                              <option value="semanal">Semanal</option>
                              <option value="quincenal">Cada 15 días</option>
                              <option value="mensual">Mensual</option>
                            </select>
                          </div>
                          <div class="col-md-3"><label class="form-label">Día de la semana / mes</label><input
                              id="mmDia" class="form-control" type="number" min="1" max="31" placeholder="1-31 o 1-7">
                            <div class="form-text">(1=Lunes … 7=Domingo para semanal)</div>
                          </div>
                          <div class="col-md-3 d-grid"><button id="mmPlanificar"
                              class="btn btn-outline-success">Planificar</button></div>
                        </div>
                        <hr>
                        <div class="calendar">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="h6 mb-0" id="mmCalTitulo">—</div>
                            <div><button class="btn btn-sm btn-outline-secondary" id="mmPrev">◀</button><button
                                class="btn btn-sm btn-outline-secondary" id="mmNext">▶</button></div>
                          </div>
                          <div class="grid text-center small text-muted mb-1">
                            <div>Lun</div>
                            <div>Mar</div>
                            <div>Mié</div>
                            <div>Jue</div>
                            <div>Vie</div>
                            <div>Sáb</div>
                            <div>Dom</div>
                          </div>
                          <div class="grid" id="mmCalGrid"></div>
                        </div>
                      </div>
                      <div class="tab-pane fade" id="mm-historial">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <h6>Entradas (compras)</h6>
                            <table class="table table-sm">
                              <thead>
                                <tr>
                                  <th>Fecha</th>
                                  <th>Kg</th>
                                  <th>Proveedor</th>
                                  <th>$</th>
                                </tr>
                              </thead>
                              <tbody id="mmEntradas"></tbody>
                            </table>
                          </div>
                          <div class="col-md-6">
                            <h6>Salidas (despachos)</h6>
                            <table class="table table-sm">
                              <thead>
                                <tr>
                                  <th>Fecha</th>
                                  <th>Kg</th>
                                  <th>Centro</th>
                                  <th>$</th>
                                </tr>
                              </thead>
                              <tbody id="mmSalidas"></tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
            </div>
          </div>
        </div>
      </section>

      <!-- HISTORIAL GLOBAL -->
      <section class="tab-pane fade" id="tab-historial">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#hist-compras"
              type="button">Compras (Entradas)</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#hist-salidas"
              type="button">Salidas (Centros)</button></li>
        </ul>
        <div class="tab-content border border-top-0 rounded-bottom p-3">
          <div class="tab-pane fade show active" id="hist-compras">
            <div class="row g-2 mb-2">
              <div class="col-md-3"><input id="hcDesde" type="date" class="form-control"></div>
              <div class="col-md-3"><input id="hcHasta" type="date" class="form-control"></div>
              <div class="col-md-3"><select id="hcMaterial" class="form-select">
                  <option value="">Todos los materiales</option>
                </select></div>
              <div class="col-md-3 d-grid"><button id="hcFiltrar" class="btn btn-outline-success">Filtrar</button></div>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Fecha</th>
                    <th>Material</th>
                    <th>Kg</th>
                    <th>Proveedor</th>
                    <th>Precio/Kg</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="hcTabla"></tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane fade" id="hist-salidas">
            <div class="row g-2 mb-2">
              <div class="col-md-3"><input id="hsDesde" type="date" class="form-control"></div>
              <div class="col-md-3"><input id="hsHasta" type="date" class="form-control"></div>
              <div class="col-md-3"><select id="hsMaterial" class="form-select">
                  <option value="">Todos los materiales</option>
                </select></div>
              <div class="col-md-3 d-grid"><button id="hsFiltrar" class="btn btn-outline-success">Filtrar</button></div>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Fecha</th>
                    <th>Material</th>
                    <th>Kg</th>
                    <th>Centro de acopio</th>
                    <th>Precio/Kg</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="hsTabla"></tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- CALENDARIO GLOBAL -->
      <section class="tab-pane fade" id="tab-calendario">
        <div class="row g-3">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-body">
                <div class="calendar">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="h5 mb-0" id="calTitulo">—</div>
                    <div><button class="btn btn-sm btn-outline-secondary" id="calPrev">◀</button> <button
                        class="btn btn-sm btn-outline-secondary" id="calNext">▶</button></div>
                  </div>
                  <div class="grid text-center small text-muted mb-1">
                    <div>Lun</div>
                    <div>Mar</div>
                    <div>Mié</div>
                    <div>Jue</div>
                    <div>Vie</div>
                    <div>Sáb</div>
                    <div>Dom</div>
                  </div>
                  <div class="grid" id="calGrid"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="card h-100">
              <div class="card-body">
                <h6>Programar nuevo despacho</h6>
                <div class="mb-2"><label class="form-label">Material</label><select id="calMat"
                    class="form-select"></select></div>
                <div class="mb-2"><label class="form-label">Frecuencia</label>
                  <select id="calFreq" class="form-select">
                    <option value="unico">Único</option>
                    <option value="semanal">Semanal</option>
                    <option value="quincenal">Cada 15 días</option>
                    <option value="mensual">Mensual</option>
                  </select>
                </div>
                <div class="mb-2"><label class="form-label">Fecha inicial</label><input id="calFecha" type="date"
                    class="form-control"></div>
                <div class="mb-2"><label class="form-label">Hora</label><input id="calHora" type="time"
                    class="form-control" value="10:00"></div>
                <div class="mb-2"><label class="form-label">Centro de acopio</label><select id="calCentro"
                    class="form-select"></select></div>
                <button id="calGuardar" class="btn btn-success w-100">Guardar</button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CENTROS / PROVEEDORES -->
      <section class="tab-pane fade" id="tab-proveedores">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">Centros de acopio & Proveedores</h5>
          <button id="btnNuevoProveedor" class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#modalProveedor">Añadir</button>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="provTabla"></tbody>
          </table>
        </div>
      </section>

      <!-- CONVERSACIONES -->
      <section class="tab-pane fade" id="tab-conversaciones">
        <div class="row g-3">
          <div class="col-lg-4">
            <div class="card p-3">
              <h6 class="mb-3">Conversaciones</h6>
              <div class="list-group sidebar-threads" id="threadList"></div>
            </div>
          </div>
          <div class="col-lg-8 d-flex flex-column">
            <div class="chat-window d-flex flex-column mb-3" id="chatWindow"></div>
            <div class="input-group">
              <input type="text" class="form-control" id="chatInput" placeholder="Escribe un mensaje…">
              <button class="btn btn-success" id="chatSend">Enviar</button>
            </div>
          </div>
        </div>
      </section>

      <!-- CONFIG -->
      <section class="tab-pane fade" id="tab-config">
        <div class="card">
          <div class="card-body">
            <h6 class="mb-3">Preferencias del punto ECA</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Mostrar en mapa público</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="cfgMapa">
                  <label class="form-check-label" for="cfgMapa">Visible cuando esté aprobado</label>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Recibir notificaciones</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="cfgNoti">
                  <label class="form-check-label" for="cfgNoti">Aprobaciones, mensajes, comentarios</label>
                </div>
              </div>
            </div>
            <div class="text-end mt-3"><button class="btn btn-success btn-sm" id="cfgGuardar">Guardar</button></div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- MODAL: Alta de proveedor/centro -->
  <div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="formProveedor">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo centro/proveedor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-12"><label class="form-label">Nombre</label><input type="text" class="form-control"
              id="provNombre" required></div>
          <div class="col-md-6"><label class="form-label">Tipo</label><select class="form-select" id="provTipo">
              <option>Centro de acopio</option>
              <option>Proveedor</option>
            </select></div>
          <div class="col-md-6"><label class="form-label">Contacto</label><input type="text" class="form-control"
              id="provContacto" required></div>
          <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" class="form-control"
              id="provTelefono" required></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control"
              id="provEmail" required></div>
          <div class="col-12"><label class="form-label">Dirección</label><input type="text" class="form-control"
              id="provDireccion" required></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-success" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TOAST -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
    <div id="toastOK" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
      aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastText">Acción realizada.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</x-app-layout>