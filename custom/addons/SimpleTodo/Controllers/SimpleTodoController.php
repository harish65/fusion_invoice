<?php

namespace Addons\SimpleTodo\Controllers;

use Addons\SimpleTodo\Repositories\SimpleTodoTaskRepository;
use Addons\SimpleTodo\Validators\SimpleTodoTaskValidator;
use FI\Http\Controllers\Controller;

class SimpleTodoController extends Controller
{
    private $taskRepository;
    private $taskValidator;

    public function __construct(
        SimpleTodoTaskRepository $taskRepository,
        SimpleTodoTaskValidator $taskValidator)
    {
        $this->taskRepository = $taskRepository;
        $this->taskValidator  = $taskValidator;
    }

    public function index()
    {
        return view('simpletodo.index')
            ->with('tasks', $this->taskRepository->paginate());
    }

    public function create()
    {
        return view('simpletodo.form')
            ->with('editMode', false);
    }

    public function store()
    {
        $validator = $this->taskValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return redirect()->route('simpleTodo.create')
                ->withErrors($validator)
                ->withInput();
        }

        $this->taskRepository->create(request()->all());

        return redirect()->route('simpleTodo.index')
            ->with('alertSuccess', trans('fi.record_successfully_created'));
    }

    public function edit($id)
    {
        return view('simpletodo.form')
            ->with('editMode', true)
            ->with('task', $this->taskRepository->find($id));
    }

    public function update($id)
    {
        $validator = $this->taskValidator->getValidator(request()->all());

        if ($validator->fails())
        {
            return redirect()->route('simpleTodo.edit', [$id])
                ->withErrors($validator)
                ->withInput();
        }

        $this->taskRepository->update(request()->all(), $id);

        return redirect()->route('simpleTodo.index')
            ->with('alertSuccess', trans('fi.record_successfully_updated'));
    }

    public function delete($id)
    {
        $this->taskRepository->delete($id);

        return redirect()->route('simpleTodo.index')
            ->with('alertSuccess', trans('fi.record_successfully_deleted'));
    }
}