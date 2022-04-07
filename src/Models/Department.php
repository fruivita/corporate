<?php

namespace FruiVita\Corporate\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lotação de um determinada usuário.
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
     * Lotação pai de uma determinada lotação.
     *
     * Relacionamento department filha (N:1) department pai.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_department', 'id');
    }

    /**
     * Lotações filhas de uma determinada lotação.
     *
     * Relacionamento department pai (1:N) department filhas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_department', 'id');
    }

    /**
     * Usuários lotadas em uma determinada lotação.
     *
     * Relacionamento department (1:N) user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }
}
