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
use App\Traits\HandlesImages;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use Response, HandlesImages;


    public function myBooks()
    {
        try {
            $data["books"] = BookResource::collection(request()->user("api")->books);
            return $this->data($data);
        } catch (\Throwable $e) {
            return $this->serverError($e->getMessage());
        }
    }


    public function profile()
    {
        $data["user"] = UserResource::make(request()->user("api"));

        return $this->data($data);
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user("api");
            $user->update($request->validated());
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
            $request->user("api")->update([
                "password" => $request->new_password
            ]);

            DB::commit();
            return $this->success("password updated successfully");
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        }
    }


    public function store(StoreBookRequest $request)
    {
        try {
            DB::beginTransaction();
            // $book = $request->validated();
            if ($request->hasFile("cover")) {
                $request->validate([
                    "cover" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
                ]);
                // $request->file('cover')->store('logos', 'public')
                // $path = $this->uploadImage(
                //     $request->file('cover'),
                //     'books/covers' // storage directory
                // );
                $book["cover"] = $request->file('cover')->store('covers' , 'public');
            }


            // $book = $request->user("api")->books()->create($book);

            // $data["book"] = BookResource::make($book);

            DB::commit();
            // return $this->data($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::info($e);
            return $this->serverError($e->getMessage());
        }
    }

    public function show(ShowBookRequest $request, $id)
    {
        try {
            $data["book"] = BookResource::make(
                $request->user("api")->books()->where("uuid", $id)->firstOrFail()
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

            $book = request()->user("api")->books()->where("uuid", $id)->firstOrFail();
            $book->update($request->validated());
            $data["book"] = BookResource::make($book);

            DB::commit();
            return $this->data($data);
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->serverError($e->getMessage());
        } catch (HttpResponseException $e) {
            DB::rollBack();
            return $e->getResponse();
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $book = request()->user("api")->books()->where("uuid", $id)->firstOrFail();
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
            return $this->data([
                "books" => BookResource::collection(
                    Book::whereNot("user_id", request()->user("api")->id)->get()
                )
            ]);
        } catch (\Throwable $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
