import {Bot} from "./Bot/Bot.js";
import IO from "./IO/IO.js";
import Config from "../config.js";

process.on("uncaughtException", (err) => {
    console.error("Uncaught Exception:", err);
});

process.on("unhandledRejection", (reason, promise) => {
    console.error("Unhandled Rejection at:", promise, "reason:", reason);
});


async function Run() {
    var bot = new Bot();
    await bot.Init();
    var io = new IO();
    await io.Init();
}

Run();