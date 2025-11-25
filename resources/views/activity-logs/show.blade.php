@extends('layouts.base')

@section('title', 'Detalle de Log - Chankas Car')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Detalle de Actividad</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm float-right">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="{{ $activityLog->icon }}"></i> Información de la Actividad
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID del Log:</dt>
                        <dd class="col-sm-9"><code>#{{ $activityLog->id }}</code></dd>

                        <dt class="col-sm-3">Usuario:</dt>
                        <dd class="col-sm-9">
                            @if($activityLog->user)
                                <strong>{{ $activityLog->user->name }}</strong>
                                <span class="badge badge-info ml-2">{{ $activityLog->user->role_name }}</span>
                            @else
                                <span class="text-muted">Sistema automático</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Acción:</dt>
                        <dd class="col-sm-9">
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
                                    'viewed' => 'Visualización',
                                    'login' => 'Inicio de sesión',
                                    'logout' => 'Cierre de sesión',
                                ];
                            @endphp
                            <span class="badge badge-{{ $badges[$activityLog->action] ?? 'secondary' }}">
                                {{ $labels[$activityLog->action] ?? $activityLog->action }}
                            </span>
                        </dd>

                        <dt class="col-sm-3">Módulo:</dt>
                        <dd class="col-sm-9">
                            @if($activityLog->model_type)
                                <span class="badge badge-light">{{ class_basename($activityLog->model_type) }}</span>
                                @if($activityLog->model_id)
                                    <small class="text-muted"> (ID: {{ $activityLog->model_id }})</small>
                                @endif
                            @else
                                <span class="text-muted">No aplica</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">{{ $activityLog->description }}</dd>

                        <dt class="col-sm-3">Fecha y Hora:</dt>
                        <dd class="col-sm-9">
                            <i class="fas fa-calendar"></i> {{ $activityLog->created_at->format('d/m/Y') }}
                            <i class="fas fa-clock ml-2"></i> {{ $activityLog->created_at->format('H:i:s') }}
                            <br>
                            <small class="text-muted">{{ $activityLog->created_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-3">Dirección IP:</dt>
                        <dd class="col-sm-9"><code>{{ $activityLog->ip_address }}</code></dd>

                        <dt class="col-sm-3">Navegador:</dt>
                        <dd class="col-sm-9">
                            <small class="text-muted">{{ $activityLog->user_agent }}</small>
                        </dd>
                    </dl>
                </div>
            </div>

            @if($activityLog->changes)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exchange-alt"></i> Cambios Registrados
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(is_array($activityLog->changes))
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 30%">Campo</th>
                                            <th style="width: 35%">Valor Anterior</th>
                                            <th style="width: 35%">Valor Nuevo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activityLog->changes as $field => $values)
                                            <tr>
                                                <td><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                                <td>
                                                    @if(isset($values['old']))
                                                        <span class="text-danger">
                                                            {{ is_array($values['old']) ? json_encode($values['old']) : $values['old'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($values['new']))
                                                        <span class="text-success">
                                                            {{ is_array($values['new']) ? json_encode($values['new']) : $values['new'] }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <pre class="bg-light p-3">{{ json_encode($activityLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            {{-- Información adicional --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información Adicional
                    </h3>
                </div>
                <div class="card-body">
                    @if($activityLog->user)
                        <h5>Actividad del Usuario</h5>
                        <p>
                            <strong>{{ $activityLog->user->name }}</strong> ha realizado 
                            <span class="badge badge-primary">
                                {{ \App\Models\ActivityLog::where('user_id', $activityLog->user->id)->count() }}
                            </span> 
                            acciones en total.
                        </p>

                        <h5 class="mt-3">Últimas Acciones</h5>
                        <ul class="list-unstyled">
                            @foreach(\App\Models\ActivityLog::where('user_id', $activityLog->user->id)
                                ->where('id', '!=', $activityLog->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get() as $recentLog)
                                <li class="mb-2">
                                    <small>
                                        <i class="{{ $recentLog->icon }}"></i>
                                        {{ Str::limit($recentLog->description, 50) }}
                                        <br>
                                        <span class="text-muted">{{ $recentLog->created_at->diffForHumans() }}</span>
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($activityLog->model_type && $activityLog->model_id)
                        <h5 class="mt-3">Historial del Registro</h5>
                        <p>
                            Este {{ class_basename($activityLog->model_type) }} tiene 
                            <span class="badge badge-info">
                                {{ \App\Models\ActivityLog::where('model_type', $activityLog->model_type)
                                    ->where('model_id', $activityLog->model_id)
                                    ->count() }}
                            </span> 
                            actividades registradas.
                        </p>
                    @endif

                    <h5 class="mt-3">Contexto Técnico</h5>
                    <p>
                        <strong>IP:</strong> {{ $activityLog->ip_address }}<br>
                        <strong>Hora exacta:</strong> {{ $activityLog->created_at->format('d/m/Y H:i:s.u') }}
                    </p>
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> Ver Todos los Logs
                    </a>
                    @if($activityLog->user)
                        <a href="{{ route('activity-logs.index', ['user_id' => $activityLog->user->id]) }}" class="btn btn-info btn-block">
                            <i class="fas fa-user"></i> Ver Logs de {{ $activityLog->user->name }}
                        </a>
                    @endif
                    @if($activityLog->model_type)
                        <a href="{{ route('activity-logs.index', ['model_type' => class_basename($activityLog->model_type)]) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-folder"></i> Ver Logs de {{ class_basename($activityLog->model_type) }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
