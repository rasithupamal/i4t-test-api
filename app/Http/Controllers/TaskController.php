<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{

    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $tasks = $this->taskRepository->getTasks(auth()->id());
            return TaskResource::collection($tasks);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $userId = auth()->id();
            $task = $this->taskRepository->create($request->validated(), $userId);
            return response()->json([
                "data" => new TaskResource($task),
                "message" => "Task created success."
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        try {
            $userId = auth()->id();

            $task = $this->taskRepository->find($task->id, $userId);
            if (!$task) {
                return response()->json('404')->setStatusCode('404');
            }
            return new TaskResource($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $userId = auth()->id();
            $task = $this->taskRepository->find($userId, $task->id);
            if (!$task) {
                return response()->json('404')->setStatusCode('404');
            }
            $this->taskRepository->update($task, $request->validated());
            return response()->json([
                "data" => new TaskResource($task),
                "message" => "Task updated success."
            ], 200);
            // return new TaskResource($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        try {
            $userId = auth()->id();
            $task = $this->taskRepository->find($task->id, $userId);
            if (!$task) {
                return response()->json('404')->setStatusCode('404');
            }
            $this->taskRepository->delete($task);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function updateTaskAsCompeleted(Task $task)
    {
        try {
            $userId = auth()->id();
            $task = $this->taskRepository->find($task->id, $userId);

            if (!$task) {
                return response()->json('404')->setStatusCode('404');
            }
            $this->taskRepository->markStatusAsCompleted($task);
            // return new TaskResource($task);
            return response()->json([
                "data" => new TaskResource($task),
                "message" => "Task mark as completed."
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
