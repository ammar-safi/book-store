<?php

namespace App\Http\Controllers\Api;

use App\Data\BooksData;
use App\Data\UserData;
use App\Http\Controllers\Controller,
    App\Http\Requests\ShowBookRequest,
    App\Http\Requests\StoreBookRequest,
    App\Http\Requests\UpdateBookRequest,
    App\Http\Requests\UpdateUserPasswordRequest,
    App\Http\Requests\UpdateUserRequest,
    App\Http\Resources\BookResource,
    App\Http\Resources\UserResource,
    App\Models\Book,
    App\Traits\FileUploader,
    App\Traits\Response,
    Illuminate\Http\Exceptions\HttpResponseException,
    Illuminate\Http\Request,
    Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use Response, FileUploader;


    public function index()
    {
        try {
            // $data["user"] = UserResource::make(
            //     request()->user()->load('books')
            // )
            // dd(Book::firstOrFail());
            // $data['test'] = BooksData::from(Book::firstOrFail()->toArray());

            $data["user"] = UserData::from(request()->user());
            return $this->data($data);
        } catch (\Throwable $e) {
            return $this->serverError($e->getMessage());
        }
    }


    public function profile()
    {
        $data["user"] = UserResource::make(request()->user());

        return $this->data($data);
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();
            $user->update($request->all());
            $data["user"] = UserResource::make($user);

            DB::commit();
            return $this->data($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        }
    }

    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->user()->update([
                "password" => $request->new_password
            ]);

            DB::commit();
            return $this->success("password updated successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        }
    }

    public function store () {

    }

    // public function store(StoreBookRequest $request)
    // {
        try {
            DB::beginTransaction();

            if ($request->hasFile("cover")) {
                $url = $this->uploadImagePublic($request, "cover", "cover");
                $url = $url ? $url : null;
            }


            $book = $request->user()->books()->create([
                "title" => $request->title,
                "description" => $request->description,
                "cover" => $url,
                "author" => $request->author
            ]);

            $data["book"] = BookResource::make($book);

            DB::commit();
            return $this->data($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        }
    // }

    public function show(ShowBookRequest $request, $id)
    {
        try {
            $data["book"] = BookResource::make(
                $request->user()->books()->whereUuid($id)->firstOrFail()
            );
            return $this->data($data);
        } catch (\Throwable $e) {
            return $this->serverError($e->getMessage());
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    public function updateBook(UpdateBookRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $book = request()->user()->books()->whereUuid($id)->firstOrFail();
            $book->update($request->validated());
            $data["book"] = BookResource::make($book);

            DB::commit();
            return $this->data($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $book = request()->user()->books()->whereUuid($id)->firstOrFail();
            $book->delete();

            DB::commit();
            return $this->success("book deleted successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }

    public function publishBooks()
    {
        try {
            $data["books"] = BookResource::collection(
                Book::whereNot("user_id", request()->user()->id)->get()
            );
            return $this->data($data);
        } catch (\Throwable $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
