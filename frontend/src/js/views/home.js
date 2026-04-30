import "../../css/views/home.css";
import * as UnicornStudio from '../external/unicornStudio.umd.js'
import lottie from "lottie-web";

var sceneConfig = {
    elementId: "unicorn",
    fps: 40,
    scale: window.innerWidth < 600 ? 0.5 : 0.6,
    dpi: 1.5,
    filePath: "/public/inex.json",
    fixed: true
};

let activeUnicornScene = null;

UnicornStudio.addScene(sceneConfig)
    .then((scene) => {
        activeUnicornScene = scene;
        document.body.classList.add("loaded");

        const canvas = document.querySelector("#unicorn canvas");
        if (canvas && typeof ResizeObserver !== "undefined") {
            new ResizeObserver(() => {
                if (canvas.offsetWidth > 0 && canvas.offsetHeight > 0) {
                    remeasureUnicornCanvasToViewport();
                }
            }).observe(canvas);
        }
    })
    .catch((err) => {
        console.error(err);
        document.body.classList.add("loaded");
    });

function remeasureUnicornCanvasToViewport() {
    if (!activeUnicornScene?.resize) return;

    const unicornContainer = document.getElementById("unicorn");
    if (unicornContainer) {
        unicornContainer.style.width = window.innerWidth + "px";
        unicornContainer.style.height = window.innerHeight + "px";
        unicornContainer.getBoundingClientRect(); // force reflow
    }

    let resizeOk = false;
    try {
        activeUnicornScene.resize();
        resizeOk = true;
    } catch (err) {
        console.error("[unicorn] resize() threw", err);
    }

    if (resizeOk && typeof activeUnicornScene.renderFrame === "function") {
        try {
            activeUnicornScene.renderFrame();
        } catch (err) {
            console.error("[unicorn] renderFrame() threw", err);
        }
    }

    if (unicornContainer) {
        unicornContainer.style.width = "";
        unicornContainer.style.height = "";
    }
}


function scheduleUnicornRefresh() {
    remeasureUnicornCanvasToViewport();
    requestAnimationFrame(remeasureUnicornCanvasToViewport);
    requestAnimationFrame(() => requestAnimationFrame(remeasureUnicornCanvasToViewport));
    /*setTimeout(remeasureUnicornCanvasToViewport, 200);
    setTimeout(remeasureUnicornCanvasToViewport, 500);
    setTimeout(remeasureUnicornCanvasToViewport, 1000);*/
}

window.addEventListener("focus", scheduleUnicornRefresh);
document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "visible") scheduleUnicornRefresh();
});
window.addEventListener("pageshow", scheduleUnicornRefresh);
window.addEventListener("resize", scheduleUnicornRefresh);

const unicornContainer = document.getElementById("unicorn");
if (unicornContainer && typeof ResizeObserver !== "undefined") {
    new ResizeObserver(remeasureUnicornCanvasToViewport).observe(unicornContainer);
}

const homePageAnimation = lottie.loadAnimation({
    container: document.getElementById("logo-anim"),
    renderer: "svg",
    autoplay: false,
    loop: false,
    path: "/public/osekai-lottie.json",
});

homePageAnimation.addEventListener("config_ready", () => {
    homePageAnimation.setSpeed(0.8);
    homePageAnimation.play();
    document.getElementById("home-welcome").classList.add("lottie-running");
    setTimeout(() => {
        document.getElementById("home-welcome").classList.add("lottie-done");
    }, 700);
    setTimeout(() => {
        document.getElementById("home-welcome").classList.add("lottie-doner");
    }, 2100);
});


