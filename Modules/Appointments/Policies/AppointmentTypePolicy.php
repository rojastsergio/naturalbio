<?php

namespace Modules\Appointments\Policies;

use App\Models\User;
use Modules\Appointments\Models\AppointmentType;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any appointment types.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view appointments');
    }

    /**
     * Determine whether the user can view the appointment type.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Appointments\Models\AppointmentType  $appointmentType
     * @return bool
     */
    public function view(User $user, AppointmentType $appointmentType)
    {
        return $user->hasPermissionTo('view appointments');
    }

    /**
     * Determine whether the user can create appointment types.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create appointments');
    }

    /**
     * Determine whether the user can update the appointment type.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Appointments\Models\AppointmentType  $appointmentType
     * @return bool
     */
    public function update(User $user, AppointmentType $appointmentType)
    {
        return $user->hasPermissionTo('update appointments');
    }

    /**
     * Determine whether the user can delete the appointment type.
     *
     * @param  \App\Models\User  $user
     * @param  \Modules\Appointments\Models\AppointmentType  $appointmentType
     * @return bool
     */
    public function delete(User $user, AppointmentType $appointmentType)
    {
        return $user->hasPermissionTo('delete appointments');
    }
}