// fiveserver.config.js
module.exports = {
    php: "C:/xamp/php/php.exe",     // Windows
    highlight: true, // enable highlight feature
    injectBody: true, // enable instant update
    remoteLogs: true, // enable remoteLogs
    remoteLogs: "yellow", // enable remoteLogs and use the color yellow
    injectCss: true, // disable injecting css
    navigate: true, // enable auto-navigation
    // debugVSCode: false,
    https: true,
    host: 'localhost',
    port: 80,
    open: false // or open your express.js app (http://localhost:3000/ for example)
}