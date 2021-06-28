<?php

namespace App\Http\Controllers;

use App\Models\Article;
use AvtoDev\JsonRpc\Errors\InvalidParamsError;
use AvtoDev\JsonRpc\Requests\RequestInterface;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{

    public function createArticle(RequestInterface $request): array
    {
        $this->checkToken();
        $params = $request->getParams();

        if($params){
            $params = json_decode(json_encode($params), true);
            $validator = Validator::make($params, [
                'author_id' => 'required|integer|min:1|exists:users,id',
                'title'     => 'required|string|min:3|max:255',
                'content'   => 'required|string|min:3|max:65535',
            ]);
            if($validator->fails()){
                throw new InvalidParamsError("Invalid params", 200, $validator->errors()->getMessages());
            }
        }

        $article = Article::create([
            "author_id" => $params["author_id"],
            "title"     => $params["title"],
            "content"   => $params["content"]
        ]);

        return $article->toArray();
    }

    public function listArticles(): array
    {
        $this->checkToken();

        return Article::with(["author"])->simplePaginate(10)->toArray();
    }

}
