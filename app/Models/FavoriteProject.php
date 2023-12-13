<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteProject extends Model
{
    protected $table = 'favorite_project';
    protected $fillable = ['created_by', 'project_id', 'title', 'supervisor_name', 'research_specialization', 'abstract'];
}