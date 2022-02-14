<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    use ResponseTrait;

    protected $book;
    protected $author;
    protected $publisher;

    public function __construct(Book $book, Author $author, Publisher $publisher){
        $this->book = $book;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->middleware('auth:api');
    }


    /**
     * Get List book
     * @OA\Get (
     *     path="/api/books",
     *     tags={"Book"},
     *     description="List all books",
     *      security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="example title"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="example content"
     *                     ),
     *                     @OA\Property(
     *                         property="publisher",
     *                         type="string",
     *                         example="Longbooks"
     *                     ),
     *                     @OA\Property(
     *                         property="author",
     *                         type="array",
     *                         @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="John Terry"
     *                          )
     *                      )
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $books = $this->book::all();
        return response()->json(["rows"=>BookResource::collection($books)]);
    }


    /**
     * Create Book
     * @OA\Post (
     *     path="/api/books",
     *     tags={"Book"},
     *     description="Add a book to publisher's collection",
     *      security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="publisher",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="author",
     *                          type="array",
     *                          @OA\Items(
     *                              type="string"
     *                          )
     *                      ),
     *                 ),
     *                 example={
     *                     "title":"example title",
     *                     "description":"example content",
     *                     "publisher":"example content",
     *                     "author": "['MacDonalds Panther', 'Koboko Black']"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="description", type="string", example="description"),
     *              @OA\Property(property="author", example="['MacDonalds Panther', 'Koboko Black']"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="Validation Error"),
     *          )
     *      )
     * )
     */
    public function store(StoreBookRequest $request)
    {
        if (auth()->user()->publisher_id == null)
        {
            return response()->json(["msg"=>"Only a publisher can edit a book item."],404);
        }
        try {
         DB::beginTransaction();

            $book = $this->book->query()
                ->firstOrCreate([
                    'title' => $request->title,
                    'description' => $request->description,
                    'publisher_id' => auth()->user()->publisher_id
                ],[
                    'title' => $request->title,
                    'description' => $request->description,
                    'publisher_id' => auth()->user()->publisher_id
                ]);
            $count = count($request->author);
            $author_ids = [];
            foreach ($request->author as $author)
            {
                $insert_author = $this->author->query()
                    ->firstOrCreate(
                        [ 'name' => $author ], ['name' => $author]
                    );
                array_push($author_ids, $insert_author->id);
            }
            $book->authors()->sync($author_ids);
            DB::commit();
            return response()->json(new BookResource($book), 201);
        } catch (\Exception $e)
        {
            report($e);
            return response()->json(["msg"=>$e->getMessage()],404);
        }

    }


    /**
     * Get Detail Books
     * @OA\Get (
     *     path="/api/books/{id}",
     *     tags={"Book"},
     *     description="Get detail of a particular book",
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="example title"
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="string",
     *                         example="example content"
     *                     ),
     *                     @OA\Property(
     *                         property="publisher",
     *                         type="string",
     *                         example="Longbooks"
     *                     ),
     *                     @OA\Property(
     *                         property="author",
     *                         type="array",
     *                         @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="John Terry"
     *                          )
     *                      )
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Book $book)
    {
        return response()->json($book);
    }



    /**
     * Update Book
     * @OA\Put (
     *     path="/api/books/{id}",
     *     tags={"Book"},
     *     description="Update a book",
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="publisher",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="author",
     *                          type="array",
     *                          @OA\Items(
     *                              type="string"
     *                          )
     *                      ),
     *                 ),
     *                 example={
     *                     "title":"example title",
     *                     "description":"example content",
     *                     "publisher":"example content",
     *                     "author":"['macbook pro', 'hp probook']",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="content", type="string", example="content"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        if (auth()->user()->publisher_id == null)
        {
            return response()->json(["msg"=>"nly a publisher can edit a book item."],404);
        }

        if ($book->publisher_id != auth()->user()->publisher_id)
        {
            return response()->json(["msg"=>"Access Denied."],404);
        }
        try {
            DB::beginTransaction();
            $publisher_id = \request()->user('api')->publisher_id;
            $result = $book->query()
                ->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'publisher_id' => $publisher_id
                ]);
            $book = $book->refresh();
            $count = count($request->author);
            $author_ids = [];
            foreach ($request->author as $author)
            {
                $insert_author = $this->author->query()
                    ->firstOrCreate(
                        [ 'name' => $author ], ['name' => $author]
                    );
                array_push($author_ids, $insert_author->id);
            }
            $book->authors()->sync($author_ids);
            DB::commit();
            return response()->json(new BookResource($book), 201);
        } catch (\Exception $e)
        {
            report($e);
            return response()->json(["msg"=>$e->getMessage()],404);
        }

    }


    /**
     * Delete Book
     * @OA\Delete (
     *     path="/api/books/{id}",
     *     tags={"Book"},
     *     description="Delete a book",
     *      security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete book success")
     *         )
     *     )
     * )
     */
    public function destroy(Book $book)
    {
        if ($book->publisher_id != auth()->user()->publisher_id)
        {
            return response()->json(["msg"=>"Access Denied."],404);
        }
        $book->authors()->detach();
        $book->delete();
    }
}
