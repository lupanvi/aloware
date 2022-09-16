<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Interfaces\CommentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentsController extends Controller
{
    private CommentRepositoryInterface $commentRepository;

    /**
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->commentRepository->getAllComments()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse
    {
        return response()->json(
            $this->commentRepository->createComment($request->toArray()),
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommentRequest $request, $id): JsonResponse
    {
        return response()->json(
            $this->commentRepository->updateComment($id, $request->toArray())
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->commentRepository->deleteComment($id);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
