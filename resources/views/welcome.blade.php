@extends('layouts.base')

@section('title', 'Laravel Claude Chatbot')

@section('content')
<div class="row">
    <div class="col-sm-6 col-md-10 col-lg-7 container px-4">
        <div class="card">
            <div id="chat-list" class="card-body" style="height: 600px; overflow-y: auto;">
            </div>
        </div>
    </div>
</div>

<div class="relative">
    <div style="position: absolute; bottom: 10px; left:0; right: 0;">
        <div class="row">
            <div class="col-sm-6 col-md-10 col-lg-7 container py-2 px-4">
                <div class="card">
                    <div class="card-body">
                        <div class="input-group input-group-flat">
                            <input id="input-mesage" type="text" class="form-control" autocomplete="off" placeholder="Type message">
                            <button id="btn-send" class="btn">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script src="https://unpkg.com/marked@12.0.1/marked.min.js"></script>
<script>
    let stillWriting = false;

    const btnSend = document.getElementById("btn-send");
    const chatListEl = document.getElementById("chat-list");
    const inputMesage = document.getElementById("input-mesage");

    const createElementFromStr = (str) => {
        const div = document.createElement('div');
        div.innerHTML = str;
        return div;
    }

    const createElementChatItem = (type) => {
        const chatItemElementTemplateBot = `
            <div class="d-flex p-2 rounded mb-2" style="background-color: #f3f4f6">
                <div class="mb-1">
                    <span class="avatar " style="background-image: url(/logo.png)"></span>
                </div>
                <div class="px-3">
                    <div class="text-muted">Chatbot</div>
                    <div id="message-content"></div>
                    <div id="scroll-item"></div>
                </div>
            </div>`;
        const chatItemElementTemplateUser = `
            <div class="d-flex p-2 rounded mb-2" style="background-color: #f3f4f6; ">
                <div class="mb-1">
                    <span class="avatar rounded-circle" style="background-image: url(https://ahmadrosid.com/profile.png)"></span>
                </div>
                <div class="px-3">
                    <div class="text-muted">You</div>
                    <div id="message-content"></div>
                </div>
            </div>`;

        let chatItemElementTemplate = chatItemElementTemplateBot;
        if (type === "user") {
            chatItemElementTemplate = chatItemElementTemplateUser;
        }

        const newElement = createElementFromStr(chatItemElementTemplate)
        newElement.className = "chat-item";
        return newElement;
    }

    const createErrorElement = (message) => {
        const templete = `
            <div class="d-flex p-2 rounded mb-2" style="background-color: #f3f4f6">
                <span class="avatar " style="background-image: url(/logo.png)"></span>
                <div class="px-3">
                    <div class="text-muted mb-2">System</div>
                    <div class="alert alert-danger bg-white" role="alert">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                            <div>${message}</div>
                        </div>
                    </div>
                </div>
            </div>`;
        const newElement = createElementFromStr(templete);
        newElement.innerHTML = templete;
        return newElement;
    }

    const triggerStreaming = (question) => {
        stillWriting = true;
        const newBotElement = createElementChatItem();
        const newUserElement = createElementChatItem("user");
        chatListEl.appendChild(newUserElement);
        const messageItemUser = newUserElement.querySelector("#message-content");
        messageItemUser.innerText = question;

        const queryQuestion = encodeURIComponent(question);
        let url = `/chat/streaming?question=${queryQuestion}`;
        const source = new EventSource(url);
        let sseText = "";

        chatListEl.appendChild(newBotElement);
        const messageContent = newBotElement.querySelector("#message-content");
        const scrollItem = newBotElement.querySelector("#scroll-item");
        scrollItem.scrollIntoView();

        source.addEventListener("update", (event) => {
            if (event.data === "<END_STREAMING_SSE>") {
                source.close();
                stillWriting = false;
                return;
            }

            const data = JSON.parse(event.data);
            if (data.text) {
                sseText += data.text;
                messageContent.innerHTML = marked.parse(sseText);
            }
            scrollItem.scrollIntoView();
        });

        source.addEventListener("error", (event) => {
            stillWriting = false;
            console.error('EventSource failed:', event);
            newBotElement.remove();
            newUserElement.remove();
            const errorEl = createErrorElement("An error occurred. Try again later.")
            chatListEl.appendChild(errorEl);
        })
    };

    function submitSendMessage() {
        if (stillWriting) {
            return;
        }

        const inputText = inputMesage.value;
        const btnRetry = document.getElementById("btn-retry");
        if (inputText != "") {
            if (btnRetry) {
                btnRetry.remove();
            }
            inputMesage.value = "";
            triggerStreaming(inputText);
        } else {
            inputText.focus();
        }
    }

    btnSend.addEventListener("click", () => {
        submitSendMessage()
    })

    inputMesage.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            submitSendMessage();
        }
    });
</script>
@endpush