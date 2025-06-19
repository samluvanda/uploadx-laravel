# UploadX Laravel

**uploadx-laravel** is the official Laravel backend integration for [UploadX](https://github.com/samluvanda/uploadx), a modern HTML5-powered JavaScript file uploader library.

It handles chunked file uploads, validation, temporary storage, and final file assembly in a clean, Laravel-native way.

---

## 📦 Installation

```bash
composer require samluvanda/uploadx-laravel
```

Then publish the config file into your Laravel app:

```bash
php artisan uploadx:install
```

This will copy `config/uploadx.php` into your application where you can freely modify the available upload profiles.

---

## ⚙️ Configuration

After publishing, the config file will be found at:

```
config/uploadx.php
```

The configuration file defines **upload profiles**, which allow you to configure behavior for different types of uploads (e.g. avatars, documents, large videos).

### 🧩 Example Profile

```php
return [
    'default' => 'default',
    'profiles' => [
        'default' => [
            'disk' => 'local',
            'path' => 'uploads',
            'combine_chunks' => true,
            'validate' => [
                'file' => ['required', 'file'],
                'chunk' => [
                    'required_with:chunks',
                    'integer',
                    'min:0',
                    function ($attribute, $value, $fail) {
                        $chunks = request()->input('chunks');
                        if (is_numeric($chunks) && $value >= (int) $chunks) {
                            $fail("The $attribute field must be less than chunks.");
                        }
                    },
                ],
                'chunks' => [
                    'required_with:chunk',
                    'integer',
                    'min:1',
                ],
                'name' => ['nullable', 'string', 'max:255'],
            ],
        ],
    ],
];
```

---

## ➕ Adding Your Own Profile

To define a new upload profile, add a new key under `profiles` in the config file. For example:

```php
'avatars' => [
    'disk' => 'public',
    'path' => 'uploads/avatars',
    'combine_chunks' => false,
    'validate' => [
        'file' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
    ],
],
```

You can now use this profile by specifying it via the request header `UploadX-Profile: avatars`.

---

## 🧠 Headers Required for Uploads

### 1. `UploadX-Profile`

- **Purpose**: Selects which config profile to apply.
- **Default**: `"default"` (from config)
- **Example**:
  ```http
  UploadX-Profile: documents
  ```

If the header is missing, the default profile (`default`) will be used.

---

### 2. `UploadX-File-Field`

- **Purpose**: Tells the server the field name of the file in the form-data.
- **Default**: `"file"`
- **Example**:
  ```http
  UploadX-File-Field: upload
  ```

If you're sending the file under a different field name in JavaScript, this tells Laravel where to find it in `$request->file()`.

---

### 3. `X-CSRF-TOKEN`

- **Purpose**: Ensures the request passes Laravel’s CSRF protection (only needed if the route uses the `web` middleware).
- **Required**: Yes, **if CSRF protection is enabled** (e.g., in `web.php`)
- **How to obtain** (in a Blade template):
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  ```
- **Example JavaScript**:
  ```js
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
  }
  ```

---

## 🛣️ Sample Route

In your `routes/web.php` or `routes/api.php`, define the upload endpoint like so:

```php
use UploadX\Controllers\UploadController;

Route::post('/upload', UploadController::class);
```

---

## 🧪 Usage Example with UploadX JS

```js
const uploader = new Uploader({
  browse_button: 'browseBtn',
  url: '/upload',
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'UploadX-Profile': 'avatars',
    'UploadX-File-Field': 'file'
  },
  filters: {
    max_file_size: '2mb',
    mime_types: [{ title: 'Images', extensions: 'jpg,jpeg,png' }]
  },
  chunk_size: '512kb',
});

uploader.bind('FileUploaded', (up, file, response) => {
  console.log('Upload complete:', response);
});

document.getElementById('startBtn').onclick = () => uploader.start();
```

---

## 🙌 Contributing

We welcome PRs, issues, and enhancements. Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## 📄 License

MIT © 2025 [Samson Luvanda](mailto:s_luvanda@hotmail.com)