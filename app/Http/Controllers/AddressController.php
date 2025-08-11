<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    /* Listar direcciones */
    public function index()
    {
        return Auth::user()->addresses()->get();
    }

    /* Crear */
    public function store(Request $request)
    {
        $data = $request->validate([
            'address_line1'      => 'required|string',
            'addres_line2'      => 'string|nullable',
            'city'       => 'required|string',
            'state'      => 'string|nullable',
            'zip'        => 'string|nullable',
            'country'    => 'string|size:2',
            'is_primary' => 'boolean',
        ]);

        if (!empty($data['is_primary'])) {
            Auth::user()->addresses()->update(['is_primary' => false]);
        }

        $address = Auth::user()->addresses()->create($data);
        return response()->json($address, 201);
    }

    /* Actualizar */
    public function update(Request $request, Address $address)
    {
        Gate::authorize('update', $address);

        $data = $request->validate([
            'line1'      => 'string',
            'line2'      => 'string|nullable',
            'city'       => 'string',
            'state'      => 'string|nullable',
            'zip'        => 'string|nullable',
            'country'    => 'string|size:2',
            'is_primary' => 'boolean',
        ]);

        if (!empty($data['is_primary'])) {
            Auth::user()->addresses()->update(['is_primary' => false]);
        }

        $address->update($data);
        return $address;
    }

    /* Borrar */
    public function destroy(Address $address)
    {
        Gate::authorize('delete', $address);
        $address->delete();
        return response()->json([], 204);
    }

    public function veterinarianAppointments()
    {
         $appointments = Appointment::with(['pet', 'client'])
            ->where('veterinarian_id', auth()->id())
            ->orderBy('date')
            ->get()
            ->map(function ($cita) {
                return [
                    'id'        => $cita->id,
                    'pet_name'  => $cita->pet->name ?? null,
                    'owner'     => $cita->client->name ?? null,
                    'date'      => \Carbon\Carbon::parse($cita->date)->format('d-m-Y'),
                    'time'      => substr($cita->time, 0, 5),
                    'reason'    => $cita->reason,
                    'status'    => $cita->status,
                ];
        });

        return response()->json($appointments);
    }

    public function veterinarianShow($id)
    {
        $appointment = Appointment::with(['pet', 'client'])
            ->where('id', $id)
            ->where('veterinarian_id', auth()->id())
            ->firstOrFail();

        return response()->json([
            'id'        => $appointment->id,
            'pet_name'  => $appointment->pet->name ?? null,
            'owner'     => $appointment->client->name ?? null,
            'date'      => \Carbon\Carbon::parse($appointment->date)->format('d-m-Y'),
            'time'      => substr($appointment->time, 0, 5),
            'reason'    => $appointment->reason,
            'status'    => $appointment->status,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendiente,completada,cancelada',
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('veterinarian_id', auth()->id())
            ->firstOrFail();

        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'message' => 'Estado de la cita actualizado.',
            'status'  => $appointment->status
        ]);
    }




}
