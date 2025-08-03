<?php

namespace App\Http\Controllers;

use App\Models\Address;
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
}
