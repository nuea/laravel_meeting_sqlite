<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;

class MeetingController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::all();
        foreach($meetings as $m){
            $m->view_meeting = [
                'href' => 'api/v1/meeting'.$m->id,
                'method' => 'GET'
            ];
        }
        $response = [
            'msg' => 'List of all Messtings',
            'meetings' => $meetings
        ];
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = new Meeting ([
            'title' => $title,
            'description' => $description,
            'time' => $time
        ]);
        
        if($meeting->save()){
            $meeting->users()->attach($user_id);
            $meeting->view_meeting = [
                'href' => 'Meeting created',
                'method' => 'GET'
            ];
            $message = [
                'msg' => 'Meetig created',
                'meeting' => $meeting
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg' => 'Error during creating'
        ];

        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meetings = Meeting::with('users')->where('id', $id)->firstOrFail();
        $meetings->view_meeting = [
            'href' => 'api/v1/meeting'.$meetings->id,
            'method' => 'GET'
        ];
        $response = [
            'msg' => 'Messting information',
            'meetings' => $meetings
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = Meeting::with('users')->findOrFail($id);
        
        if(!$meeting->users()->where('users.id', $user_id)->first()){
            return response()->json(['msg' => 'user not registerd for meeting, update not succesful'], 401);
        }

        $meeting->time = $time;
        $meeting->title = $title;
        $meeting->description = $description;

        if(!$meeting->update()){
            return reponse()->json(['msg' => 'Error during update'],404);
        }

        $meeting->view_meeting = [
            'href' => 'api/v1/meeting'.$meeting->id,
            'method' => 'GET'
        ];

        $response = [
            'msg' => 'Messting Updated',
            'meeting' => $meeting
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->user;
        $meeting->users()->detach();

        if(!$meeting->delete()){
            foreach($users as $us){
                $meeting->users()->attach($user);
            }
            return response()->json(['msg' => 'Deletion Failed'], 404);
        }
        $response = [
            'msg' => 'Messting deleted',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title,description, time'
            ]
        ];
        return response()->json($response, 200);
    }
}
