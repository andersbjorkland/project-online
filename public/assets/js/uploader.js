(function() {
    const document = window.document;
    const hostServer = document.location.origin;
    console.log("uploader.js is loaded");
    const target = hostServer;

    const HOST = target + "/admin/file-upload";
    console.log("Host target: " + HOST);

    addEventListener("trix-attachment-add", function(event) {
        console.log('Event registered.');
        if (event.attachment.file) {
            console.log('Event contains file.');
            uploadFileAttachment(event.attachment)
        }
    })

    function uploadFileAttachment(attachment) {
        console.log(attachment);
        uploadFile(attachment.file, setProgress, setAttributes)

        function setProgress(progress) {
            attachment.setUploadProgress(progress)
        }

        function setAttributes(attributes) {
            attachment.setAttributes(attributes)
        }
    }

    function uploadFile(file, progressCallback, successCallback) {
        var key = createStorageKey(file);
        var formData = createFormData(key, file);
        console.log(formData, key, file);


        fetch(HOST, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(response => response.json())
            .then(json => {
                console.log('Success: ', json);
                var attributes = {
                    url: target + json.filepath,
                    href: target + json.filepath + "?content-disposition=attachment"
                }
                successCallback(attributes)
            })
            .catch(error => {
                console.error('Error: ', error);
            });
    }


    function createStorageKey(file) {
        var date = new Date()
        var day = date.toISOString().slice(0,10)
        var name = date.getTime() + "-" + file.name
        return [ "tmp", day, name ].join("/")
    }

    function createFormData(key, file) {
        var data = new FormData()
        data.append("key", key)
        data.append("Content-Type", file.type)
        data.append("file", file)
        return data
    }
})();