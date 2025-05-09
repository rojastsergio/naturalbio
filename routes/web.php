<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RoleController;
use Modules\Appointments\Controllers\AppointmentController;
use Modules\Appointments\Controllers\AppointmentTypeController;
use Modules\Appointments\Controllers\DoctorAvailabilityController;
use Modules\Doctors\Controllers\DoctorController;
use Modules\Therapies\Controllers\TherapyController;
use Modules\Therapies\Controllers\TherapyAssignmentController;
use Modules\Therapies\Controllers\TherapyProgressController;
use Modules\Prescriptions\Controllers\PrescriptionController;
use Modules\Supplements\Controllers\SupplementController;
use Modules\Recommendations\Controllers\RecommendationController;
// Eliminado el controlador duplicado
// use Modules\Therapies\Controllers\TherapyInstructionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para el módulo de Pacientes (implementadas a través de PatientController)
    Route::get('/patients', [\Modules\Patients\Controllers\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [\Modules\Patients\Controllers\PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [\Modules\Patients\Controllers\PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [\Modules\Patients\Controllers\PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [\Modules\Patients\Controllers\PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [\Modules\Patients\Controllers\PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [\Modules\Patients\Controllers\PatientController::class, 'destroy'])->name('patients.destroy');
    
    // Rutas para signos vitales
    Route::post('/patients/{patient}/vitals', [\Modules\Patients\Controllers\VitalSignController::class, 'store'])->name('vitals.store');
    Route::put('/vitals/{vitalSign}', [\Modules\Patients\Controllers\VitalSignController::class, 'update'])->name('vitals.update');
    Route::delete('/vitals/{vitalSign}', [\Modules\Patients\Controllers\VitalSignController::class, 'destroy'])->name('vitals.destroy');
    
    // Rutas para el módulo de Citas - Ahora con menos restricciones para poder acceder inicialmente
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update']);
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    
    // Rutas para tipos de citas y disponibilidad de doctores
    Route::resource('appointment-types', AppointmentTypeController::class);
    Route::resource('doctor-availabilities', DoctorAvailabilityController::class);
    
    // Rutas para el módulo Doctores (definidas manualmente para mantener consistencia)
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    
    // Rutas específicas del doctor
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/availability', [\Modules\Doctors\Controllers\DoctorAvailabilityController::class, 'index'])->name('doctor.availability');
    Route::get('/doctor/therapy-instructions', [\Modules\Doctors\Controllers\TherapyInstructionController::class, 'index'])->name('doctor.therapy-instructions');
    
    // Rutas para el módulo de Terapias
    Route::get('/therapies', [TherapyController::class, 'index'])->name('therapies.index');
    Route::get('/therapies/create', [TherapyController::class, 'create'])->name('therapies.create');
    Route::post('/therapies', [TherapyController::class, 'store'])->name('therapies.store');
    Route::get('/therapies/{therapy}', [TherapyController::class, 'show'])->name('therapies.show');
    Route::get('/therapies/{therapy}/edit', [TherapyController::class, 'edit'])->name('therapies.edit');
    Route::put('/therapies/{therapy}', [TherapyController::class, 'update'])->name('therapies.update');
    Route::delete('/therapies/{therapy}', [TherapyController::class, 'destroy'])->name('therapies.destroy');
    
    // Rutas para asignaciones de terapias
    Route::get('/therapy-assignments', [TherapyAssignmentController::class, 'index'])->name('therapy-assignments.index');
    Route::get('/therapy-assignments/{assignment}', [TherapyAssignmentController::class, 'show'])->name('therapy-assignments.show');
    Route::patch('/therapy-assignments/{assignment}', [TherapyAssignmentController::class, 'update'])->name('therapy-assignments.update');
    
    // Rutas para instrucciones terapéuticas - ELIMINADAS PARA EVITAR CONFLICTO
    // Ya están definidas en el archivo de rutas del módulo Therapies mediante Route::resource()
    
    // Rutas para el módulo de Recetas
Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
Route::get('/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
Route::get('/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
Route::post('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'generatePdf'])->name('prescriptions.pdf');
Route::post('/prescriptions/{prescription}/notify', [PrescriptionController::class, 'sendNotification'])->name('prescriptions.notify');

// Rutas para el módulo de Suplementos
Route::get('/supplements', [SupplementController::class, 'index'])->name('supplements.index');
Route::get('/supplements/create', [SupplementController::class, 'create'])->name('supplements.create');
Route::post('/supplements', [SupplementController::class, 'store'])->name('supplements.store');
Route::get('/supplements/{supplement}', [SupplementController::class, 'show'])->name('supplements.show');
Route::get('/supplements/{supplement}/edit', [SupplementController::class, 'edit'])->name('supplements.edit');
Route::put('/supplements/{supplement}', [SupplementController::class, 'update'])->name('supplements.update');
Route::delete('/supplements/{supplement}', [SupplementController::class, 'destroy'])->name('supplements.destroy');
Route::post('/supplements/{supplement}/remove-media', [SupplementController::class, 'removeMedia'])->name('supplements.remove-media');

// Rutas para el módulo de Recomendaciones
Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
Route::get('/recommendations/create', [RecommendationController::class, 'create'])->name('recommendations.create');
Route::post('/recommendations', [RecommendationController::class, 'store'])->name('recommendations.store');
Route::get('/recommendations/{recommendation}', [RecommendationController::class, 'show'])->name('recommendations.show');
Route::get('/recommendations/{recommendation}/edit', [RecommendationController::class, 'edit'])->name('recommendations.edit');
Route::put('/recommendations/{recommendation}', [RecommendationController::class, 'update'])->name('recommendations.update');
Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy'])->name('recommendations.destroy');

// Rutas adicionales para acciones específicas de recomendaciones
Route::post('/recommendations/{recommendation}/accept', [RecommendationController::class, 'acceptOrReject'])->name('recommendations.accept');
Route::post('/recommendations/{recommendation}/task', [RecommendationController::class, 'completeTask'])->name('recommendations.task');

// Ruta para mostrar las recomendaciones de un paciente específico
Route::get('/patients/{patient}/recommendations', [RecommendationController::class, 'forPatient'])->name('patients.recommendations');

    // Definir el middleware de verificación de roles como una clase
    Route::prefix('settings')
        ->name('settings.')
        ->middleware(['auth:sanctum', 'verified'])
        ->group(function () {
            // Middleware de verificación manual (sin usar Closure directamente en el array)
            Route::middleware([\Spatie\Permission\Middleware\RoleMiddleware::class.':owner'])
                ->group(function () {
                    // Rutas de configuración generales
                    Route::get('/', [SettingsController::class, 'index'])->name('index');
                    Route::get('/modules', [SettingsController::class, 'modules'])->name('modules');
                    Route::get('/forms', [SettingsController::class, 'forms'])->name('forms');
                    
                    // Rutas de gestión de roles y permisos (usando resource)
                    Route::resource('roles', RoleController::class);
                });
        });
});