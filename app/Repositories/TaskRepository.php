<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data, int $userId)
    {

        return Task::create([
            "user_id" => $userId,
            "title" => $data['title'],
            "description" => $data['description'],
            "status" => $data['status']
        ]);
    }

    public function update(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        $task->delete();
    }

    public function markStatusAsCompleted(Task $task)
    {
        $task->update(['status' =>  "COMPLETED"]);
        return $task;
    }

    public function getTasks($userId)
    {
        return Task::where('user_id', $userId)->get();
    }


    public function find($id, $userId)
    {
        return Task::where('user_id', $userId)->where('id', $id)->first();
    }
}
