<x-dynamic-component :component="$getFieldWrapperView()" :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()" :helper-text="$getHelperText()"
    :hint="$getHint()" :hint-action="$getHintAction()" :hint-color="$getHintColor()" :hint-icon="$getHintIcon()" :required="$isRequired()" :state-path="$getStatePath()">
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
        <a id="startRecording"
            class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-microphone" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M9 2m0 3a3 3 0 0 1 3 -3h0a3 3 0 0 1 3 3v5a3 3 0 0 1 -3 3h0a3 3 0 0 1 -3 -3z"></path>
                <path d="M5 10a7 7 0 0 0 14 0"></path>
                <path d="M8 21l8 0"></path>
                <path d="M12 17l0 4"></path>
            </svg>

            Start
        </a>
        <a id="stopRecording"
            class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-gray-800 bg-white border-gray-300 hover:bg-gray-50 focus:ring-primary-600 focus:text-primary-600 focus:bg-primary-50 focus:border-primary-600 filament-page-button-action">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-microphone-off" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M3 3l18 18"></path>
                <path d="M9 5a3 3 0 0 1 6 0v5a3 3 0 0 1 -.13 .874m-2 2a3 3 0 0 1 -3.87 -2.872v-1"></path>
                <path d="M5 10a7 7 0 0 0 10.846 5.85m2 -2a6.967 6.967 0 0 0 1.152 -3.85"></path>
                <path d="M8 21l8 0"></path>
                <path d="M12 17l0 4"></path>
            </svg>
            Stop
        </a>
        <br />
        <p id="isRecording">Click start button to record</p>
        <audio src="" id="audioElement" controls></audio>
        {{-- <input x-ref="input" type="file" dusk="filament.forms.{{ $getStatePath() }}" id="file" /> --}}
        <script>
            document
                .getElementById("startRecording")
                .addEventListener("click", initFunction);
            let isRecording = document.getElementById("isRecording");

            function initFunction() {
                // Display recording
                async function getUserMedia(constraints) {
                    if (window.navigator.mediaDevices) {
                        return window.navigator.mediaDevices.getUserMedia(constraints);
                    }
                    let legacyApi =
                        navigator.getUserMedia ||
                        navigator.webkitGetUserMedia ||
                        navigator.mozGetUserMedia ||
                        navigator.msGetUserMedia;
                    if (legacyApi) {
                        return new Promise(function(resolve, reject) {
                            legacyApi.bind(window.navigator)(constraints, resolve, reject);
                        });
                    } else {
                        alert("Browser not supported");
                    }
                }
                isRecording.textContent = "Recording...";
                //
                let audioChunks = [];
                let rec;

                function handlerFunction(stream) {
                    rec = new MediaRecorder(stream);
                    rec.start();
                    rec.ondataavailable = (e) => {
                        audioChunks.push(e.data);
                        if (rec.state == "inactive") {
                            let data = {};

                            let blob = new Blob(audioChunks, {
                                type: "audio/mp3"
                            });
                            console.log(blob);
                            document.getElementById("audioElement").src = URL.createObjectURL(blob);
                            populateFileInput(blob);
                        }
                    };
                }

                function populateFileInput(blob) {
                    // Create a new File object from the Blob
                    const file = new File([blob], 'recorded_audio.mp3', {
                        type: blob.type
                    });

                    // Create a custom FileList object with a single file
                    const fileList = new DataTransfer();
                    fileList.items.add(file);

                    // Get the file input element
                    const fileInput = document.getElementById('file');

                    // Assign the custom FileList object to the file input
                    fileInput.files = fileList.files;
                }

                function startusingBrowserMicrophone(boolean) {
                    getUserMedia({
                        audio: boolean
                    }).then((stream) => {
                        handlerFunction(stream);
                    });
                }
                startusingBrowserMicrophone(true);
                // Stoping handler
                document.getElementById("stopRecording").addEventListener("click", (e) => {
                    rec.stop();
                    isRecording.textContent = "Click play button to start listening";
                });
            }
        </script>
    </div>
</x-dynamic-component>
