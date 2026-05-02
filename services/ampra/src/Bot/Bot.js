
import {Client, GatewayIntentBits} from "discord.js";
import Config from "../../config.js";


export class BotIO {
    static client = null;

    static GetChannel(key) {
        const guild = this.client?.guilds?.cache?.get(Config.bot.server);
        if (!guild) {
            console.error("Guild does not exist or bot is not logged in");
            return null;
        }

        return guild.channels.cache.find(channel => channel.name === key) ?? null;
    }
}


export class Bot {
    Init() {
        return new Promise(async (resolve, reject) => {
            const client = new Client({
                intents: [
                    GatewayIntentBits.Guilds,
                    GatewayIntentBits.GuildMembers,
                    GatewayIntentBits.GuildBans,
                    GatewayIntentBits.GuildEmojisAndStickers,
                    GatewayIntentBits.GuildIntegrations,
                    GatewayIntentBits.GuildWebhooks,
                    GatewayIntentBits.GuildInvites,
                    GatewayIntentBits.GuildVoiceStates,
                    GatewayIntentBits.GuildPresences,
                    GatewayIntentBits.GuildMessages,
                    GatewayIntentBits.GuildMessageReactions,
                    GatewayIntentBits.GuildMessageTyping,
                    GatewayIntentBits.DirectMessages,
                    GatewayIntentBits.DirectMessageReactions,
                    GatewayIntentBits.DirectMessageTyping,
                    GatewayIntentBits.MessageContent,
                    GatewayIntentBits.GuildScheduledEvents
                ]
            });
            BotIO.client = client;

            client.on('ready', () => {
                console.log(`Logged in as ${client.user.tag}!`);
                resolve(true);
            });

            try {
                await client.login(Config.bot.token);
            } catch (error) {
                reject(error); // Reject the promise if login fails
            }
        });
    }
}