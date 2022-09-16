<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    /**
     * Display a listing of nested comments.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllComments(): Collection
    {
        $comments = DB::table('comments')->select('id', 'name', 'message', 'parent_id as comments')->whereNull('parent_id')->get();
        $subcomments1 = DB::table('comments')->select('id', 'name', 'message', 'parent_id as comments')->whereIn('parent_id', $comments->pluck('id'))->get();
        $grouped_subcomments1 =  $subcomments1->groupBy('comments');
        $subcomments2 = DB::table('comments')->select('id', 'name', 'message', 'parent_id as comments')->whereIn('parent_id', $subcomments1->pluck('id'))->get();
        $grouped_subcomments2 =  $subcomments2->groupBy('comments');

        foreach ($comments as $comment) {
            $comment->comments = $grouped_subcomments1[$comment->id] ?? [];
            foreach ($comment->comments as $key => $subcomment) {
                $comment->comments[$key]->comments = $grouped_subcomments2[$subcomment->id] ?? [];
                foreach ($comment->comments[$key]->comments as $subkey => $lastsubcomment) {
                    $comment->comments[$key]->comments[$subkey]->comments = [];
                }
            }
        }

        return $comments;
    }

    /**
     * Create a new comment
     *
     * @param array commentDetails
     * @return bool
     */
    public function createComment(array $commentDetails): bool
    {
        return DB::table('comments')->insert([
            'name' => $commentDetails['name'],
            'message' => $commentDetails['message'],
            'parent_id' => $commentDetails['parent_id'] ?? null
        ]);
    }

    /**
     * Update a comment
     *
     * @param int $commentId
     * @param array $newDetails
     * @return int
     */
    public function updateComment(int $commentId, array $newDetails): int
    {
        return DB::table('comments')
            ->where('id', $commentId)
            ->update([
                'name' => $newDetails['name'],
                'message' => $newDetails['message']
            ]);
    }

    /**
     * Delete a comment
     *
     * @param int $commentId
     * @return void
     */
    public function deleteComment(int $commentId): void
    {
        DB::table('comments')
            ->where('id', $commentId)
            ->delete();
    }
}
