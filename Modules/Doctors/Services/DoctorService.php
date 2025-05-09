<?php

namespace Modules\Doctors\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Doctors\Models\Doctor;
use Modules\Appointments\Models\Appointment;
use Spatie\Permission\Models\Role;

class DoctorService
{
    /**
     * Obtener todos los doctores, aplicando filtros si existen.
     */
    public function getAllDoctors(Request $request)
    {
        return Doctor::with('user')
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('specialty', 'like', "%{$search}%")
                ->orWhere('license_number', 'like', "%{$search}%");
            })
            ->when($request->specialty, function ($query, $specialty) {
                return $query->where('specialty', $specialty);
            })
            ->when(Auth::check() && Auth::user()->clinic_id, function ($query) {
                return $query->where('clinic_id', Auth::user()->clinic_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Crear un nuevo doctor.
     */
    public function createDoctor(array $data)
    {
        // Asignar la clínica del usuario autenticado
        if (Auth::check() && Auth::user() && isset(Auth::user()->clinic_id)) {
            $data['clinic_id'] = Auth::user()->clinic_id;
        } else {
            throw new \Exception('El usuario no tiene una clínica asignada');
        }

        // Crear el doctor
        $doctor = Doctor::create($data);

        // Asignar el rol de doctor al usuario si no lo tiene
        $user = \App\Models\User::find($data['user_id']);
        $doctorRole = Role::where('name', 'doctor')->first();
        
        if ($doctorRole && !$user->hasRole('doctor')) {
            $user->assignRole($doctorRole);
        }

        return $doctor;
    }

    /**
     * Actualizar un doctor existente.
     */
    public function updateDoctor(Doctor $doctor, array $data)
    {
        return $doctor->update($data);
    }

    /**
     * Eliminar un doctor.
     */
    public function deleteDoctor(Doctor $doctor)
    {
        return $doctor->delete();
    }

    /**
     * Obtener el doctor asociado al usuario actual.
     */
    public function getCurrentDoctor()
    {
        if (!Auth::check()) {
            return null;
        }
        
        return Doctor::where('user_id', Auth::id())->first();
    }

    /**
     * Obtener datos para el dashboard del doctor.
     */
    public function getDashboardData(Doctor $doctor)
    {
        $today = Carbon::today();
        $endOfWeek = Carbon::today()->endOfWeek();

        // Citas próximas
        $upcomingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->where('start_time', '>=', now())
            ->with('patient.user')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Citas de hoy
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('start_time', $today)
            ->with('patient.user')
            ->orderBy('start_time')
            ->get();

        // Citas para esta semana
        $weekAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('start_time', '>=', $today)
            ->whereDate('start_time', '<=', $endOfWeek)
            ->with('patient.user')
            ->orderBy('start_time')
            ->get();

        // Contar pacientes activos (pacientes con citas en los últimos 3 meses)
        $activePatients = Appointment::where('doctor_id', $doctor->id)
            ->where('start_time', '>=', now()->subMonths(3))
            ->distinct('patient_id')
            ->count('patient_id');

        // Calcular ingresos del mes actual
        $monthlyIncome = Appointment::where('doctor_id', $doctor->id)
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->sum('total_price');

        return [
            'upcomingAppointments' => $upcomingAppointments,
            'todayAppointments' => $todayAppointments,
            'weekAppointments' => $weekAppointments,
            'activePatients' => $activePatients,
            'monthlyIncome' => $monthlyIncome,
        ];
    }
}