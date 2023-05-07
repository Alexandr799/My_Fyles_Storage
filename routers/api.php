<?php
use App\Controllers\DirectoryMiddlewares\Middlewares\DeleteDirectoryValidator;
use App\Controllers\DirectoryMiddlewares\Middlewares\DirectoryAddValidator;
use App\Controllers\DirectoryMiddlewares\Middlewares\UpdateDirValidator;
use App\Controllers\File;
use App\Controllers\FilesMiddlewares\Middlewares\DeleteFileValidator;
use App\Controllers\FilesMiddlewares\Middlewares\FileAddValidator;
use App\Controllers\FilesMiddlewares\Middlewares\UpdateFileValidator;
use App\Controllers\ShareMiddlewares\Middlewares\ShareInfoValidator;
use App\Controllers\ShareMiddlewares\Middlewares\ShareValidator;
use App\Controllers\User;
use App\Controllers\UserMiddlewares\Middlewares\Admin;
use App\Controllers\UserMiddlewares\Middlewares\Auth;
use App\Controllers\UserMiddlewares\Middlewares\LoginValidation;
use App\Controllers\UserMiddlewares\Middlewares\RegisterValidator;
use App\Controllers\UserMiddlewares\Middlewares\UpdateValidator;
use App\Controllers\UserMiddlewares\Middlewares\ValidID;
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
    Admin::create()->next(User::create()),
    'list'
);

Router::get(
    '/api/admin/user/{id}',
    Admin::create()->next(User::create()),
    'index'
);

Router::delete(
    '/api/admin/user/{id}',
    Admin::create()->next(User::create()),
    'delete'
);

Router::put(
    '/api/admin/user',
    Admin::create()->next(UpdateValidator::create()->next(User::create())),
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


