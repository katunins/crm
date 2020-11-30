<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    // Возвращает коллекцию с задачами и likes
    static function getTasks()
    {
        $tasks = DB::table('Tasks')->get();
        $likesArr = DB::table('Likes')->get();

        foreach ($tasks as $key => $task) {
            $task->likes = $likesArr->where('taskId', $task->id)->count(); //->setAttribute('likes', 3);
        }
        return $tasks->sortByDesc('likes');
    }

    public function setNewTask(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Укажите задачу'
        ]);


        DB::table('tasks')->insert([
            'name' => $request->name,
        ]);
        return redirect()->back()->with('info', 'Задача "' . $request->name . '" добавлена!');
    }

    public function setLike(Request $request)
    {
        $token = Session::get('_token');
        $likesDublicates = DB::table('likes')->where([
            'taskId' => $request->id,
            'userId' => $token
        ])->get()->count();
        $likesDublicates = 0;
        if ($likesDublicates == 0) DB::table('likes')->insert([
            'taskId' => $request->id,
            'userId' => $token
        ]);
        return response()->json($likesDublicates == 0 ? true : false);
    }
}
