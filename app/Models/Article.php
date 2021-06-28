<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Chat
 *
 * @property int $id
 * @property int $author_id
 * @property string|null $title
 * @property string|null $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $author
 *
 * @package App\Models
 */
class Article extends Model
{
	protected $table = 'articles';

	protected $fillable = [
		'author_id',
		'title',
		'content'
	];

	public function author()
	{
		return $this->belongsTo(User::class, 'author_id');
	}
}
