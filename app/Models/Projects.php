<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Projects extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'projects';
    protected $fillable = ['name', 'file'];

   

}

class FavoriteProject extends Model
{
    protected $table = 'favorite_project';
    protected $fillable = ['created_by', 'project_id', 'title', 'supervisor_name', 'research_specialization', 'abstract'];
}
