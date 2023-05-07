<?php

use App\Controllers\File;
use App\Controllers\Middlewares\DirectoryMiddlewares\DeleteDirectoryValidator;
use App\Controllers\Middlewares\DirectoryMiddlewares\DirectoryAddValidator;
use App\Controllers\Middlewares\DirectoryMiddlewares\UpdateDirValidator;
use App\Controllers\Middlewares\FilesMiddlewares\DeleteFileValidator;
use App\Controllers\Middlewares\FilesMiddlewares\FileAddValidator;
use App\Controllers\Middlewares\FilesMiddlewares\UpdateFileValidator;
use App\Controllers\Middlewares\ShareMiddlewares\ShareInfoValidator;
use App\Controllers\Middlewares\ShareMiddlewares\ShareValidator;
use App\Controllers\Middlewares\UserMiddlewares\Admin;
use App\Controllers\Middlewares\UserMiddlewares\Auth;
use App\Controllers\Middlewares\UserMiddlewares\LoginValidation;
use App\Controllers\Middlewares\UserMiddlewares\RegisterValidator;
use App\Controllers\Middlewares\UserMiddlewares\UpdateValidator;
use App\Controllers\Middlewares\UserMiddlewares\ValidID;
use App\Controllers\User;
use App\Entities\Router;

// пути важно писать в строго формате начиная со слэша /test/path а не path/test , также при создании api, любой путь важно начить со слов api
// например /api/test

// пути важно писать в строго формате начиная со слэша /test/path а не path/test

Router::get(
    '/api/user/{id}',
    Auth::create()->next(ValidID::create()->next(User::create())),
    'index'
);

Router::post(
    '/api/user',
    RegisterValidator::create()->next(User::create()),
    'store'
);

Router::put(
    '/api/user',
    Auth::create()->next(ValidID::create()->next(UpdateValidator::create()->next(User::create()))),
    'update'
);

Router::delete(
    '/api/user/{id}',
    Auth::create()->next(ValidID::create()->next(User::create())),
    'delete'
);

Router::get(
    '/api/login',
    LoginValidation::create()->next(User::create()),
    'login'
);

Router::get(
    '/api/reset_password',
    User::create(),
    'reset_password'
);

Router::get(
    '/api/logout',
    Auth::create()->next(User::create()),
    'logout'
);

Router::get(
    '/api/admin/user',
    Auth::create()->next(Admin::create()->next(User::create())),
    'list'
);

Router::get(
    '/api/admin/user/{id}',
    Auth::create()->next(Admin::create()->next(User::create())),
    'index'
);

Router::delete(
    '/api/admin/user/{id}',
    Auth::create()->next(Admin::create()->next(User::create())),
    'delete'
);

Router::put(
    '/api/admin/user',
    Auth::create()->next(Admin::create()->next(UpdateValidator::create()->next(User::create()))),
    'update'
);

Router::get(
    '/api/file',
    Auth::create()->next(File::create()),
    'fileAll'
);

Router::get(
    '/api/file/{id}',
    Auth::create()->next(File::create()),
    'file'
);

Router::post(
    '/api/file',
    Auth::create()->next(FileAddValidator::create()->next(File::create())),
    'addFile'
);

Router::get(
    '/api/file/{id}',
    Auth::create()->next(File::create()),
    'file'
);

Router::get(
    '/api/file',
    Auth::create()->next(File::create()),
    'fileAll'
);

Router::put(
    '/api/file',
    Auth::create()->next(UpdateFileValidator::create()->next(File::create())),
    'updateFile'
);

Router::delete(
    '/api/file/{id}',
    Auth::create()->next(DeleteFileValidator::create()->next(File::create())),
    'deleteFile'
);

Router::post(
    '/api/directory',
    Auth::create()->next(DirectoryAddValidator::create()->next(File::create())),
    'addDirectory'
);

Router::put(
    '/api/directory',
    Auth::create()->next(UpdateDirValidator::create()->next(File::create())),
    'renameDirectory'
);

Router::get(
    '/api/directory/{id}',
    Auth::create()->next(File::create()),
    'infoDirectory'
);

Router::delete(
    '/api/directory/{id}',
    Auth::create()->next(DeleteDirectoryValidator::create()->next(File::create())),
    'deleteDirectory'
);

Router::get(
    '/api/user/search/{email}',
    Auth::create()->next(User::create()),
    'getByEmail'
);

Router::put(
    '/api/files/share/{id}/{user_id}',
    Auth::create()->next(ShareValidator::create()->next(File::create())),
    'shareFile'
);

Router::get(
    '/api/files/share/{id}',
    Auth::create()->next(ShareInfoValidator::create()->next(File::create())),
    'shareFileInfo'
);

Router::delete(
    '/api/files/share/{id}/{user_id}',
    Auth::create()->next(File::create()),
    'deleteShareFile'
);
