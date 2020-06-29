<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserCompany as Company;
use App\UserDepartment as Department;
use App\UserLocation as Location;

class UserController extends Controller
{
    /**
     * Get the user's profile as JSON itself.
     * 
     * Examples:
     * return $request->user();
     * return App\User::with(['pan', 'right'])->find($request->user()->id); 
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function self(Request $request)
    {
        return $request->user()->getSelfWithRelations();
    }


    // Get Companies / Departments / Locations
    public function getCompanies(Request $request) 
    {
        return $request->user()->companies->toArray();
    }

    public function getDepartments(Request $request) 
    {
        return $request->user()->departments->toArray();
    }

    public function getLocations(Request $request) 
    {
        return $request->user()->locations->toArray();
    }


    // Set IDs
    public function setCompanyId(Request $request)
    {
        $usr = $request->user();
        if($usr->companies()->allowed()->find($request->id) || $request->id === null) {
            $usr->company_id = $request->id;
            $usr->save();
        }
    }

    public function setDepartmentId(Request $request)
    {
        $usr = $request->user();
        if($usr->departments()->allowed()->find($request->id) || $request->id === null ) {
            $usr->department_id = $request->id;
            $usr->save();
        }
    }

    public function setLocationId(Request $request)
    {
        $usr = $request->user();
        if($usr->locations()->allowed()->find($request->id) || $request->id === null ) {
            $usr->location_id = $request->id;
            $usr->save();
        }
    }

    // Create Company / Location / Department
    public function createCompany(Request $request) 
    {
        $m = Company::create([
            'name' => $request->item['name'],
            'created_by' => $request->user()->id,
        ]);
        return $m->toJson();
    }

    public function createDepartment(Request $request) 
    {
        $m = Department::create([
            'name' => $request->item['name'],
            'created_by' => $request->user()->id,
        ]);
        return $m->toJson();
    }

    public function createLocation(Request $request) 
    {
        $m = Location::create([
            'name' => $request->item['name'],
            'created_by' => $request->user()->id,
        ]);
        return $m->toJson();
    }

    // Update Compnay Location Department
    public function updateCompany(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.id' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;
        $m = $request->user()->companies()->allowed()->find($item['id']);
        $m->name = $item['name'];
        $m->save();
    }

    public function updateDepartment(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.id' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;
        $m = $request->user()->departments()->allowed()->find($item['id']);
        $m->name = $item['name'];
        $m->save();
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.id' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;
        $m = $request->user()->locations()->allowed()->find($item['id']);
        $m->name = $item['name'];
        $m->save();
    }
}
