HTMLElement.prototype.setValue = function (val) {
    this.innerText = val;
};
HTMLInputElement.prototype.setValue = function (val) {
    this.value = val;
};
HTMLFormElement.prototype.checkValidity = function () {
    const valid = Array.from(this.querySelectorAll("[name]")).reduce((a, c) => {
        return c?.validity?.valid && a;
    }, true);
    return valid;
};
HTMLFormElement.prototype.getData = function () {
    const data = Array.from(this.querySelectorAll("[name]")).reduce((a, c) => {
        let fldName = c.getAttribute("name");
        let fldType = c.getAttribute("type");
        switch (fldType) {
            case "radio":
            case "checkbox":
                if (!c.checked) return a;
                break;
            default:
                break;
        }
        if (!(fldName in a)) {
            a[fldName] = c.value;
            return a;
        }
        if (a[fldName] instanceof Array) {
            a[fldName].push(c.value);
            return a;
        }
        a[fldName] = [a[fldName], c.value];
        return a;
    }, {});
    return data;
};
HTMLFormElement.prototype.getInvalidInputs = function () {
    return Array.from(this.querySelectorAll(":invalid"));
};
HTMLFormElement.prototype.hasFile = function () {
    return !!this.querySelector("[type=file]");
};
HTMLElement.prototype.showError = function (error) {
    let parent = this.closest(".form-group") ?? this.parentElement;
    parent.classList.add("has-error");
    let er = parent.querySelector(".error");
    if (!er) {
        er = document.createElement("span");
        er.classList.add("error");
        parent.append(er);
    }
    er.innerText = error;
};
HTMLElement.prototype.clearError = function () {
    let parent = this.closest(".form-group") ?? this.parentElement;
    parent.classList.remove("has-error");
    let er = parent.querySelector(".error");
    if (er) {
        er.remove();
    }
};
Array.prototype.rotate = function (direction = "right") {
    let array = this;
    switch (direction) {
        case "left":
            let firstElement = array[0];
            array.shift();
            array.push(firstElement);
            break;
        case "right":
            let lastElement = array.pop();
            array.unshift(lastElement);
            break;
    }
};
String.prototype.slug = function () {
    let exp = RegExp(/\W/g);
    return this.replace(exp, "-").toLowerCase();
};
Number.prototype.toDataSize = function (precision = 3) {
    let bytes = this;
    const units = ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    let i = 0;
    while (bytes >= 1024) {
        bytes /= 1024;
        i++;
    }
    return `${bytes.toFixed(3)} ${units[i]}`;
};
Date.prototype.Format = function () {
    let format = Intl.DateTimeFormat("en-in", { year: "numeric", month: "2-digit", day: "2-digit" });
    return format.format(this);
}
class API {
    #headers = {
        "Content-Type": "application/json",
    };
    #endpoint = "";
    constructor(noauth = false, endpoint = "") {
        if (endpoint !== "") this.#endpoint = endpoint;
        if (noauth) {
            return;
        }
        let token = localStorage.getItem("token");
        if (!!token) {
            this.header = {
                Authorization: `Bearer ${token}`
            }
        }
    }
    async execute(url, method = "GET", data = null) {
        try {
            let options = {
                method: method,
                headers: this.#headers,
            };
            if (data) options.body = JSON.stringify(data);
            const response = await fetch(this.#endpoint + url, options);
            let resp = await response.json();
            if (resp.status === "error" && resp.message === "Token Expired") {
                localStorage.clear();
                window.location.href = "/";
            }
            return resp;

        } catch (error) {
            return false;
        }
    }
    async get(url) {
        return this.execute(url);
    }
    async post(url, data) {
        return this.execute(url, "POST", data);
    }
    async put(url, data) {
        return this.execute(url, "PUT", data);
    }
    async patch(url, data) {
        return this.execute(url, "PATCH", data);
    }
    async delete(url, data) {
        return this.execute(url, "DELETE", data);
    }
    async copy(url, data) {
        return this.execute(url, "COPY", data);
    }
    async getHTML(url) {
        let response = await fetch(url);
        return response.text();
    }
    get headers() {
        return this.#headers;
    }
    set headers(headers) {
        this.#headers = headers;
    }
    get endpoint() {
        return this.#endpoint;
    }
    set endpoint(endpoint) {
        this.#endpoint = endpoint;
    }
    set header(object) {
        Object.assign(this.#headers, object);
    }
}
class WebAlert {
    constructor(resp) {
        if (!resp.status) return;
        if (resp.status == "success" && resp.message) { alert(resp.message); location.reload(); }
        if (resp.status == "error" && resp.message) { alert(resp.message); }
    }
}
const api = new API();
class FormScreen {
    constructor(resp) {
        if (!("next" in resp)) return;
        const card = document.querySelector(".card");
        if (!card) return
        const cardTitle = card.querySelector(".card-title");
        const cardBody = card.querySelector(".card-body");
        if (!cardTitle || !cardBody) return
        cardTitle.innerText=resp.next.title??"";
        cardBody.innerHTML=`<p>${resp.next.message??""}</p>`;
        if(resp.data){
            let list=document.createElement("ul");
            list.classList.add("nolist");
            resp.data.forEach(listItem=>{
                let item=document.createElement("li");
                item.innerHTML=`${listItem.name}<span>${listItem.value}</span>`;
                list.append(item);
            })
            cardBody.append(list);
        }
        if(resp.next.action){
            let flexCenter=document.createElement("div");
            flexCenter.classList.add("flex-center");
            // flexCenter.classList.add("justify-content-center");

            let actionButton=document.createElement("a");
            actionButton.classList.add("btn")
            actionButton.classList.add("btn-primary")
            actionButton.setAttribute("href",resp.next.action);
            actionButton.innerText="Next"
            flexCenter.append(actionButton);
            cardBody.append(flexCenter);
        }
        if(resp.next.url){
            api
                .get(resp.next.url)
                .then(resp=>{
                    new FormScreen(resp);
                })
        }
    }
}

async function startProcessing() {
    const card = document.querySelector(".card")
    const cardTitle = card.querySelector(".card-title");
    const cardBody = card.querySelector(".card-body");
    let resp = await api.get("/csv/loadtemp");
    console.log(resp);
    cardTitle.innerText = "Processing...";
    cardBody.innerHTML = "";

}

async function validateAndSubmit(e) {
    e.preventDefault();
    const target = e.target;
    const form = target.tagName === "FORM" ? target : target.form;
    if (!form.checkValidity()) {
        let invalidFields = form.getInvalidInputs();
        invalidFields.forEach((inp) => {
            inp.validity.valueMissing
                ? inp.showError("Required")
                : inp.showError("Invalid");
            inp.addEventListener(
                "keyup",
                () => {
                    inp.clearError();
                },
                { once: true },
            );
        });
        invalidFields[0].focus();
        return;
    }
    const action = form.getAttribute("action");
    const method = form.getAttribute("method");
    if (form.hasFile()) {
        const fileInput = form.querySelector("[type=file]");
        const formData = new FormData(form);
        formData.append(fileInput.name, fileInput.files[0]);
        const xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', function (event) {
            const progress = form.querySelector("#progress");
            if (event.lengthComputable) {
                progress.removeAttribute("hidden");
                const percentComplete = (event.loaded / event.total) * 100;
                progress.innerText = percentComplete.toFixed(2)
            }
        });
        xhr.onload = function () {
            if (xhr.status === 200) {
                const progress = form.querySelector("#progress");
                progress.setAttribute("hidden", true);
                let resp = JSON.parse(xhr.responseText);
                new FormScreen(resp);
            } else {
            }
        };
        xhr.open('POST', action);
        xhr.send(formData);
    } else {
        const api = new API();
        const data = form.getData();
        const resp = await api[method.toLowerCase()](action, data);
        new WebAlert(resp)
        return resp;
    }
};
async function loaddata(elm){
    let parent=elm.closest("[seed]");
    if(!parent)return;
    elm.innerText="";
    elm.classList.add("loading");
    elm.classList.remove("btn")
    elm.classList.remove("btn-primary")
    elm.classList.remove("btn-sm");
    let resp=await api.get(`/seed/${parent.getAttribute("seed")}`);
    elm.classList.remove("loading");
    if("entries" in resp){
        elm.innerText=resp.entries;
    }
}
(async () => {
    let inputs = document.querySelectorAll("input");
    inputs.forEach(inp => {
        inp.addEventListener("blur", () => {
            inp.classList.add("blurred")
        })
    })
})();

(async () => {
    let tblgrps = document.querySelectorAll("[tbl]");
    if (tblgrps.length == 0) return;
    let refreshBtn = document.querySelector("[btn-refresh]");
    if (!refreshBtn) return;
    let nextBtn = document.querySelector("[btn-next]");
    if (!nextBtn) return;
    tblgrps.forEach(tbl => {
        const tableName = tbl.getAttribute("tbl");
        const statusIndicator = tbl.querySelector("span");
        statusIndicator.setAttribute("class", "loading");
        api.get(`/table/${tableName}`).then(resp => {
            if (!resp.status) return;
            statusIndicator.setAttribute("class", resp.status);
            if (document.querySelectorAll(".failed").length) {
                refreshBtn.removeAttribute("disabled");
                refreshBtn.removeAttribute("hidden");
                nextBtn.setAttribute("disabled", true);
                nextBtn.setAttribute("hidden", true);
            } else {
                refreshBtn.setAttribute("disabled", true);
                refreshBtn.setAttribute("hidden", true);
                nextBtn.removeAttribute("disabled");
                nextBtn.removeAttribute("hidden");
            }
        })
    })
})();