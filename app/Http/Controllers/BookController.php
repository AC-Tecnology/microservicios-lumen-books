<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;

class BookController extends Controller
{
    /**
     * podemos probar las exceptions de
     * /app/Exceptions/Handler.php y las rutas en el URI
     */

    use ApiResponser;       //para acceder a los metodos del trait
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    /**
     * Return book List
     * @return Illuminate\Http\Response
     */

    public function index()
    {
        $books = Book::all();
        return $this->successResponse($books);
    }

    /**
     * Create a instance of Book
     * @return Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $rules = [
            'title'         => 'required|max:255',
            'description'   => 'required|max:255',
            'price'         => 'required|numeric|min:1',
            'author_id'     => 'required|numeric|min:1',
        ];

        $this->validate($request, $rules);

        $book = Book::create($request->all());

        //HTTP_CREATED es una constante que devuelve 201 de creado en http
        return $this->successResponse($book, Response::HTTP_CREATED);
    }

    /**
     * Return a specific books
     * @return Illuminate\Http\Response
     */

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return $this->successResponse($book);
    }

    /**
     * Update the informations of a book
     * @return Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $rules = [
            'title'         => 'max:255',
            'description'   => 'max:255',
            'price'         => 'numeric|min:1',
            'author_id'     => 'numeric|min:1',
        ];

        $this->validate($request, $rules);

        $book = Book::findOrFail($id);

        $book->fill($request->all());

        //si no hay cambios
        if ($book->isClean()) {

            //HTTP_CREATED es una conmtante que devuelve 422  que no puede ser procesada
            return $this->errorResponse('Al menos un valor debe cambiar', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $book->save();

        return $this->successResponse($book);

    }

    /**
     * Remove the informations of a book
     * @return Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $book->delete();
        
        return $this->successResponse($book);
    }
}
