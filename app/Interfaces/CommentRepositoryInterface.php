<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function getAllComments(): Collection;
    public function createComment(array $commentDetails): bool;
    public function updateComment(int $commentId, array $newDetails): int;
    public function deleteComment(int $commentId): void;
}
