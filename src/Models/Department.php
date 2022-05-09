<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Department (Lotação) of a given user.
 *
 * @see https://laravel.com/docs/9.x/eloquent
 */
class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = ['id', 'parent_department', 'name', 'acronym'];

    public $incrementing = false;

    /**
     * Parent department of a given department.
     *
     * Relationship child department (N:1) parent department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_department', 'id');
    }

    /**
     * Child departments of a given department.
     *
     * Relationship parent department (1:N) child department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_department', 'id');
    }

    /**
     * Users assigned to a certain department.
     *
     * Relationship department (1:N) user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }
}
