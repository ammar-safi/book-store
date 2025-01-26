<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowBookRequest;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\UserResource;
use App\Models\Book;
use App\Traits\FileUploader;
use App\Traits\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use Response, FileUploader;


    public function index()
    {
        try {
            $data["user"] = UserResource::make(
                request()->user()->load('books')
            );
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

    public function store(StoreBookRequest $request)
    {
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
    }

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
