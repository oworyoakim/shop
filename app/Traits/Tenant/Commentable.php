<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 31/01/2020
 * Time: 8:25 AM
 */

namespace App\Traits\Tenant;


use App\Models\Tenant\Comment;

trait Commentable
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
