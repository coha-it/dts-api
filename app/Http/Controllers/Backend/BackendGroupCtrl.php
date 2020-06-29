<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User as User;
use App\Group as Group;

class BackendGroupCtrl extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'auth.user.email',
            'auth.user.can_create_users'
        ]);
    }

    public function getGroupsModerating(Request $request) {
        return $request->user()->groupsModerating->each(function ($i, $k) {
            $i->makeVisible(['description_mods']);
        })->toJson();
    }

    public function getGroup(Request $request) {
        $request->validate([
            'id' => 'required|int'
        ]);


        $group = $request->user()->groupsModerating->find($request->id);
        $response = $group->toArray();
        $response['users'] = $group->users;

        return $response;
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;
        $self = $request->user();

        $g = Group::create([
            'name' => $item['name'],
            'description_public' => $item['description_public'] ?? null,
            'description_mods' => $item['description_mods'] ?? null,
            'created_by' => $self->id,
        ]);

        $g->moderators()->sync([
            $self->id => [
                'is_mod' => 1,
                'is_member' => 0
            ],
        ]);

        return $g->toJson();
    }

    // Update Compnay Location Department
    public function updateGroup(Request $request)
    {
        $request->validate([
            'item' => 'required',
            'item.id' => 'required',
            'item.name' => 'required'
        ]);
        $item = $request->item;

        $m = $request->user()->groupsModerating()->find($item['id']);
        $m->name = $item['name'];
        $m->description_public = $item['description_public'];
        $m->description_mods = $item['description_mods'];
        $m->save();

        return $m->toJson();
    }

    // public function addUserToGroup(Request $request) {
    //     // Get
    //     $group = $request->user()->groupsModerating->find($request->group_id);
    //     $user = $request->user()->users->find($request->user_id);
    //     // Sync!
    //     $group->users()->attach($user);
    // }

    // public function removeUserFromGroup(Request $request) {
    //     // Get
    //     $self = $request->user();
    //     $group = Group::find($request->group_id);
    //     $user = $self->users->find($request->user_id);
    //     // Sync!
    //     $group->users()->detach($user);
    // }

}
