<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\User;

class AppointmentController extends Controller
{
    //

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'date'   => 'required|date|after_or_equal:today',
            'time'   => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:255',
        ]);

        // Verificar que la mascota pertenezca al usuario autenticado
        $pet = Pet::where('id', $request->pet_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Evitar duplicados: misma mascota, fecha y hora
        $exists = Appointment::where('pet_id', $pet->id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Ya existe una cita para esta mascota en esa fecha y hora.'], 409);
        }

        // Buscar veterinario disponible en ese horario
        $availableVet = User::whereHas('roles', fn($q) => $q->where('name', 'veterinario'))
            ->whereDoesntHave('appointmentsAsVet', function ($query) use ($request) {
                $query->where('date', $request->date)
                    ->where('time', $request->time);
            })
            ->inRandomOrder()
            ->first();

        if (!$availableVet) {
            return response()->json(['error' => 'No hay veterinarios disponibles en ese horario.'], 422);
        }

        $appointment = Appointment::create([
            'pet_id'          => $pet->id,
            'client_id'       => auth()->id(),
            'veterinarian_id' => $availableVet->id,
            'date'            => $request->date,
            'time'            => $request->time,
            'reason'          => $request->reason,
            'status'          => 'pendiente',
        ]);

        return response()->json([
            'message'     => 'Cita agendada correctamente.',
            'appointment' => $appointment
        ], 201);
    }


    public function clientAppointments()
    {
        $appointments = Appointment::with(['pet'])
            ->where('client_id', auth()->id())
            ->orderByDesc('date')
            ->get()
            ->map(function ($cita) {
                return [
                    'id'         => $cita->id,
                    'pet_name'   => $cita->pet->name ?? null,
                    'date'       => \Carbon\Carbon::parse($cita->date)->format('d-m-Y'),
                    'time'       => substr($cita->time, 0, 5),
                    'reason'     => $cita->reason,
                    'status'     => $cita->status,
                ];
            });

        return response()->json($appointments);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendiente,confirmada,completada,cancelada'
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('veterinarian_id', auth()->id())
            ->firstOrFail();

        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'status' => $appointment->status,
        ]);
    }


}
