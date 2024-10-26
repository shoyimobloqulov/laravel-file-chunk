## php.ini configuration varable
<pre>
upload_max_filesize = 5000M
post_max_size = 5000M
max_input_time = 3000
max_execution_time = 3000
</pre>

# Frontend setup
* Install the <a href='http://resumablejs.com/'>resuumable</a> plugin by using CDN
<pre>https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js</pre>

* Write HTML code
```html
<div class="container pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Upload File</h5>
                </div>

                <div class="card-body">
                    <div id="upload-container" class="text-center">
                        <button id="browseFile" class="btn btn-primary">Brows File</button>
                    </div>
                    <div  style="display: none" class="progress mt-3" style="height: 25px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%; height: 100%">75%</div>
                    </div>
                </div>

                <div class="card-footer p-4" style="display: none">
                    <video id="videoPreview" src="" controls style="width: 100%; height: auto"></video>
                </div>
            </div>
        </div>
    </div>
</div>
```

* Write JavaScript to upload large files via resumable in chunks


```js
let browseFile = $('#browseFile');
let resumable = new Resumable({
    target: '{{ route('files.upload.large') }}',
    query:{_token:'{{ csrf_token() }}'} ,
    fileType: ['mp4'],
    chunkSize: 10*1024*1024, 
    headers: {
        'Accept' : 'application/json'
    },
    testChunks: false,
    throttleProgressCallbacks: 1,
});

resumable.assignBrowse(browseFile[0]);

resumable.on('fileAdded', function (file) { 
    showProgress();
    resumable.upload() 
});

resumable.on('fileProgress', function (file) { 
    updateProgress(Math.floor(file.progress() * 100));
});

resumable.on('fileSuccess', function (file, response) { 
    response = JSON.parse(response)
    $('#videoPreview').attr('src', response.path);
    $('.card-footer').show();
});

resumable.on('fileError', function (file, response) { 
    alert('file uploading error.')
});


let progress = $('.progress');
function showProgress() {
    progress.find('.progress-bar').css('width', '0%');
    progress.find('.progress-bar').html('0%');
    progress.find('.progress-bar').removeClass('bg-success');
    progress.show();
}

function updateProgress(value) {
    progress.find('.progress-bar').css('width', `${value}%`)
    progress.find('.progress-bar').html(`${value}%`)
}

function hideProgress() {
    progress.hide();
}
```

# The backend setup
* Install <a href='https://github.com/pionl/laravel-chunk-upload'>laravel-chunk-upload</a> in your Laravel project
```php
public function uploadLargeFiles(Request $request) {
    $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

    if (!$receiver->isUploaded()) {
        // file not uploaded
    }

    $fileReceived = $receiver->receive(); 
    if ($fileReceived->isFinished()) { 
        $file = $fileReceived->getFile();
        $extension = $file->getClientOriginalExtension();
        $fileName = str_replace('.'.$extension, '', $file->getClientOriginalName());

        $fileName .= '_' . md5(time()) . '.' . $extension;

        $disk = Storage::disk(config('filesystems.default'));
        $path = $disk->putFileAs('videos', $file, $fileName);

        unlink($file->getPathname());
        return [
            'path' => asset('storage/' . $path),
            'filename' => $fileName
        ];
    }

    $handler = $fileReceived->handler();
    return [
        'done' => $handler->getPercentageDone(),
        'status' => true
    ];
}
```