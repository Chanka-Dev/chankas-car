@extends('layouts.base')

@section('title', 'Logs de Actividad - Chankas Car')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Registro de Actividades del Sistema</h1>
        </div>
        <div class="col-sm-6">
            <button type="button" class="btn btn-success btn-sm float-right" onclick="exportarLogs()">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
        </div>
    </div>
@stop

@section('content')
    {{-- Estadísticas --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_logs'] }}</h3>
                    <p>Total de Logs</p>
                </div>
                <div class="icon">
                    <i class="fas fa-database"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['today_logs'] }}</h3>
                    <p>Logs Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['this_week_logs'] }}</h3>
                    <p>Logs Esta Semana</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['by_action']['deleted'] ?? 0 }}</h3>
                    <p>Eliminaciones</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trash"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico de acciones --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Acciones Registradas
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="actionsChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-1"></i>
                        Usuarios Más Activos
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['top_users'] as $userLog)
                                <tr>
                                    <td>{{ $userLog->user->name }}</td>
                                    <td><span class="badge badge-info">{{ $userLog->user->role_name }}</span></td>
                                    <td class="text-right"><strong>{{ $userLog->total }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros Avanzados --}}
    <div class="card collapsed-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter"></i> Filtros Avanzados
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" style="display: none;">
            <form method="GET" action="{{ route('activity-logs.index') }}" id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Usuario</label>
                            <select name="user_id" class="form-control">
                                <option value="">Todos los usuarios</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Acción</label>
                            <select name="action" class="form-control">
                                <option value="">Todas las acciones</option>
                                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>
                                    <i class="fas fa-plus"></i> Creación
                                </option>
                                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>
                                    Actualización
                                </option>
                                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>
                                    Eliminación
                                </option>
                                <option value="viewed" {{ request('action') === 'viewed' ? 'selected' : '' }}>
                                    Visualización
                                </option>
                                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>
                                    Inicio de sesión
                                </option>
                                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>
                                    Cierre de sesión
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Módulo</label>
                            <select name="model_type" class="form-control">
                                <option value="">Todos los módulos</option>
                                @foreach($modelTypes as $type)
                                    <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Desde</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label>Búsqueda en descripción</label>
                            <input type="text" name="search" class="form-control" placeholder="Buscar en descripciones..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-block" title="Limpiar filtros">
                                <i class="fas fa-eraser"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabla de Logs --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Actividades Registradas
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $logs->total() }} registros</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th style="width: 140px">Fecha/Hora</th>
                            <th style="width: 120px">Usuario</th>
                            <th style="width: 100px">Acción</th>
                            <th style="width: 100px">Módulo</th>
                            <th>Descripción</th>
                            <th style="width: 100px">IP</th>
                            <th style="width: 80px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $logs->firstItem() + $loop->index }}</td>
                                <td>
                                    <small>
                                        <i class="fas fa-calendar"></i> {{ $log->created_at->format('d/m/Y') }}<br>
                                        <i class="fas fa-clock"></i> {{ $log->created_at->format('H:i:s') }}
                                    </small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <strong>{{ $log->user->name }}</strong><br>
                                        <small class="text-muted">{{ $log->user->role_name }}</small>
                                    @else
                                        <span class="text-muted">Sistema</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badges = [
                                            'created' => 'success',
                                            'updated' => 'primary',
                                            'deleted' => 'danger',
                                            'viewed' => 'info',
                                            'login' => 'success',
                                            'logout' => 'secondary',
                                        ];
                                        $labels = [
                                            'created' => 'Creación',
                                            'updated' => 'Actualización',
                                            'deleted' => 'Eliminación',
                                            'viewed' => 'Vista',
                                            'login' => 'Login',
                                            'logout' => 'Logout',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $badges[$log->action] ?? 'secondary' }}">
                                        {{ $labels[$log->action] ?? $log->action }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model_type)
                                        <span class="badge badge-light">{{ class_basename($log->model_type) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($log->description, 80) }}
                                    @if($log->changes)
                                        <br><small class="text-info"><i class="fas fa-info-circle"></i> Tiene cambios registrados</small>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                                <td>
                                    <button type="button" class="btn btn-info btn-xs" onclick="verDetalles({{ $log->id }})" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    No se encontraron registros con los filtros aplicados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
            <div class="card-footer clearfix">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Gráfico de acciones
        const ctx = document.getElementById('actionsChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Creaciones', 'Actualizaciones', 'Eliminaciones', 'Vistas', 'Logins'],
                datasets: [{
                    data: [
                        {{ $stats['by_action']['created'] ?? 0 }},
                        {{ $stats['by_action']['updated'] ?? 0 }},
                        {{ $stats['by_action']['deleted'] ?? 0 }},
                        {{ $stats['by_action']['viewed'] ?? 0 }},
                        {{ $stats['by_action']['login'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#28a745',
                        '#007bff',
                        '#dc3545',
                        '#17a2b8',
                        '#ffc107'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Ver detalles del log
        function verDetalles(logId) {
            window.location.href = '/activity-logs/' + logId;
        }

        // Exportar a Excel (placeholder)
        function exportarLogs() {
            const form = document.getElementById('filter-form');
            const params = new URLSearchParams(new FormData(form)).toString();
            
            mostrarInfo('Próximamente', 'La funcionalidad de exportación a Excel estará disponible próximamente.');
            
            // TODO: Implementar exportación real
            // window.location.href = '/activity-logs/export?' + params;
        }
    </script>
@endpush