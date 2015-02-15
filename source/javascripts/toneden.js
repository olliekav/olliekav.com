// Music player
function tonedenInit() {
  ToneDenReady = window.ToneDenReady || [];
  var configOne = {
    dom: "#player-1",
    urls: [
      "https://soundcloud.com/olliekav/ollie-k-dnb-mix-270513"
    ]
  }
  var configTwo = {
    dom: "#player-2",
    urls: [
      "https://soundcloud.com/olliekav/ollie-k-drum-bass-mix-2000"
    ]
  }
  var configThree = {
    dom: "#player-3",
    urls: [
      "https://soundcloud.com/olliekav/drum-and-bass-mix-2005"
    ]
  }
  var configFour = {
    dom: "#player-4",
    urls: [
      "https://soundcloud.com/olliekav/olliek-mixjune04"
    ]
  }
  var configFive = {
    dom: "#player-5",
    urls: [
      "https://soundcloud.com/olliekav/ollie-k-house-techno-mix-21-02"
    ]
  }
  var configSix = {
    dom: "#player-6",
    urls: [
      "https://soundcloud.com/olliekav/ollie-k-house-techno-mix-19-05"
    ]
  }
  var configSeven = {
    dom: "#player-7",
    urls: [
      "https://soundcloud.com/olliekav/house-techno-mix-12-12-08"
    ]
  }

  // ToneDenReady.push(function() {
  //   ToneDen.player.create(configOne);
  //   ToneDen.player.create(configTwo);
  //   ToneDen.player.create(configThree);
  //   ToneDen.player.create(configFour);
  //   ToneDen.player.create(configFive);
  //   ToneDen.player.create(configSix);
  //   ToneDen.player.create(configSeven);
  // }); 

  if(typeof ToneDen != 'undefined'){
    ToneDen.player.create(configOne);
    ToneDen.player.create(configTwo);
    ToneDen.player.create(configThree);
    ToneDen.player.create(configFour);
    ToneDen.player.create(configFive);
    ToneDen.player.create(configSix);
    ToneDen.player.create(configSeven);
  } else {
    ToneDenReady.push(function() {
      ToneDen.player.create(configOne);
      ToneDen.player.create(configTwo);
      ToneDen.player.create(configThree);
      ToneDen.player.create(configFour);
      ToneDen.player.create(configFive);
      ToneDen.player.create(configSix);
      ToneDen.player.create(configSeven);
    }); 
  }
}

tonedenInit();

document.addEventListener("page:load", function() {
  tonedenInit();
});