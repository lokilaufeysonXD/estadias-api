<?php

namespace App\Http\Controllers;

use App\Models\landings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Routing\Controller as BaseController;

class landingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function getAllLandings(Request $request)
    {
        $authenticatedCompany = auth()->user();
        if (!$authenticatedCompany) {
            return response()->json(['error' => 'No autorizado'], 401);
        }
        $query = Landings::select('landings.id', 'landings.logo', 'landings.slugs', 'landings.hero', 'landings.company_id', 'landings.services', 'landings.packages', 'company.name')
            ->leftJoin('company', 'landings.company_id', '=', 'company.id')
            ->where('landings.activo', true);
    
        if ($authenticatedCompany->mail === 'techpech@protonmail.mx') {
            $landings = $query->get();
        } else {
            $landings = $query->where('landings.company_id', $authenticatedCompany->id)->get();
        }
        return response()->json(['landings' => $landings]);
    }

    public function showlandings($id)
    {
        $landings = landings::where('id', $id)->first();
        return response($landings);
    }
    public function showlandingsBySlug($slug)
    {
        $landing = landings::where('slugs', $slug)->first();
    
        if ($landing) {
            return response()->json($landing);
        } else {
            return response()->json(['message' => 'Landing not found'], 404);
        }
    }
    public function insertLandings(Request $request)
    {
        $landings = new Landings();
        $landings->slugs = $request->slugs;
        $landings->logo = $request->logo;
        $landings->hero = $request->hero;
        $landings->services = $request->services;
        $landings->packages = $request->packages;
        $landings->company_id = $request->company_id;
        $landings->activo = true;
        $landings->save();
    }

    public function deleteLandings($id)
    {
        $landings = landings::where('id', $id)->first();
        if (!$landings) {
            return response()->json(["error" => "landings not found"]);
    }
        $landings->activo = false;
        $landings->save();
            return response()->json(["data" => "Landing with id $id hidden successfully"]);
    }

    public function updatelandings(Request $request, $id)
{  
    $landings = Landings::find($id);

    if (!$landings) {
        return response()->json(['error' => 'Landing not found'], 404);
    }

    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(env('FRONTEND_PUBLIC_PATH'), $filename);
        $landings->logo = $filename;
    } elseif ($request->has('logo')) {
        $landings->logo = $request->input('logo');
    }

    if ($request->has('hero')) {
        $hero = json_decode($request->input('hero'), true);
        
        if ($request->hasFile('background')) {
            $file = $request->file('background');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(env('FRONTEND_PUBLIC_PATH'), $filename);
            $hero['background'] = $filename;
        }
        
        $landings->hero = json_encode($hero);
    }

    if ($request->has('company_id')) {
        $landings->company_id = $request->input('company_id');
    }

    $landings->save();

    
    return response()->json(['data' => 'Se actualizó correctamente', 'landing' => $landings]);

    
}

public function getAllLandingslg(Request $request)
{
        $landings = Landings::select('landings.id', 'landings.logo', 'landings.slugs', 'landings.hero', 'landings.company_id', 'landings.services', 'landings.packages', 'company.name')
            ->leftJoin('company', 'landings.company_id', '=', 'company.id')
            ->get();

    return response()->json(['landings' => $landings]);
}

public function showlandingslg($id)
    {
        $landings = landings::where('id', $id)->first();
        return response($landings);
    }
    public function showlandingsBySluglg($slug)
    {
        $landing = landings::where('slugs', $slug)->first();
    
        if ($landing) {
            return response()->json($landing);
        } else {
            return response()->json(['message' => 'Landing not found'], 404);
        }
    }


    public function getAllLandingsm(Request $request)
    {
            $landings = Landings::select('landings.id', 'landings.logo', 'landings.slugs', 'landings.hero', 'landings.company_id', 'landings.services', 'landings.packages', 'company.name')
                ->leftJoin('company', 'landings.company_id', '=', 'company.id')
                ->get();
    
        return response()->json(['landings' => $landings]);
    }
    
    public function showlandingsm($id)
        {
            $landings = landings::where('id', $id)->get();
            return response($landings);
        }
}