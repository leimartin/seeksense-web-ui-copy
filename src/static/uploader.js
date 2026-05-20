const zip = new JSZip();
const dropZone = document.getElementById('droppr');

async function url2file(filename, b64url) {
    const blob = await (await fetch(b64url)).blob();
    return new File([blob], filename, {
        type: b64url.split(";")[0].split(":")[1]
    });
}

function initDropZone(element, handleFile) {
    const fileInput = element.querySelectorAll('input[type="file"]')[0];
    fileInput.multiple = true;

    const extToMimes = {
        'jpg': 'image/jpeg',
        'png': 'image/png',
    };

    async function file2b64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = (error) => reject(error);
        });
    }

    async function handleFiles(files) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            console.log(file);
            if (file.type == "application/zip") {
                const zipFile = await zip.loadAsync(file);
                console.log(zipFile);

                for (const filepath of Object.keys(zipFile.files)) {
                    console.log(filepath)
                    const filename = filepath.split("/").slice(-1)[0];
                    if (!filename) continue;
                    let fileb64 = "data:";
                    fileb64 += extToMimes[filename.split(".").slice(-1)[0]];
                    fileb64 += ";base64,";
                    fileb64 += await zipFile.file(filepath).async("base64");
                    handleFile(filename, fileb64);
                }
            } else if (["image/jpeg", "image/png"].includes(file.type)) {
                handleFile(file.name, await file2b64(file));
            }
        }
        console.log('Files:', files);
        // Your code to handle the selected files goes here
    }

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => handleFiles(fileInput.files));
}

