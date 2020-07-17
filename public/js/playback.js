!function(t){var e={};function n(i){if(e[i])return e[i].exports;var o=e[i]={i:i,l:!1,exports:{}};return t[i].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=t,n.c=e,n.d=function(t,e,i){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:i})},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=86)}({1:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i={id:"",vibesIDs:[],notifications:[],notificationsLoading:!0,routes:{vibes:"/user/vibes",attributes:"user/attributes",notifications:"user/notifications"},getID:function(){var t=this;return axios.get(this.routes.attributes).then(function(e){t.id=e.data.id}).catch(function(t){return console.log(t)})},getVibesIDs:function(){var t=this;return axios.get(this.routes.vibes).then(function(e){t.vibesIDs=e.data}).catch(function(t){return console.log(t)})},getAccessToken:function(){var t=this,e=new Date;e.setHours(e.getHours()-1);var n=e.getTime();return new Promise(function(e,i){if(!(localStorage.token_set_at>=n))return axios.get(t.routes.attributes).then(function(t){localStorage.token_set_at=new Date(t.data.token_set_at).getTime(),localStorage.access_token=t.data.access_token,e(localStorage.access_token)}).catch(function(t){i(t.response.data.errors)});e(localStorage.access_token)})},getNotifications:function(){var t=this;return axios.get(this.routes.notifications).then(function(e){t.notifications=e.data,t.notificationsLoading=!1}).catch(function(t){console.log(t)})},notficationsIsEmpty:function(){return 0===Object.keys(this.notifications).length},updateVibesIDs:function(t){t.auto_dj?this.vibesIDs=this.vibesIDs.filter(function(e){return e!==t.id}):this.vibesIDs.push(t.id)},allVibesIDsExcept:function(t){return this.vibesIDs.filter(function(e){return e!==t})}};window.user=i,e.default=i},2:function(t,e,n){"use strict";var i={all:[],show:{},showID:"",message:"",deletedMessage:"",loading:!0,user:n(1).default,playingTracks:{},playingID:"",routes:{index:"/vibe",create:"/vibe",show:function(t){return"/vibe/"+t},update:function(t){return"/vibe/"+t},delete:function(t){return"/vibe/"+t},autoRefresh:function(t){return"/auto-vibe/"+t},syncVibe:function(t){return"sync/vibe/"+t},syncPlaylist:function(t){return"sync/playlist/"+t},acceptJoinRequest:function(t){return"/join-request/accept/"+t},rejectJoinRequest:function(t){return"/join-request/reject/"+t},sendJoinRequest:function(t){return"/join-request/vibe/"+t},cancelJoinRequest:function(t){return"/join-request/delete/"+t},leaveVibe:function(t){return"/user-vibe/vibe/"+t},joinVibe:function(t){return"/user-vibe/vibe/"+t},removeUser:function(t,e){return"/user-vibe/vibe/"+t+"/user/"+e},removeTrack:function(t,e){return"/track-vibe/vibe/"+t+"/track/"+e},addTrack:function(t,e){return"/track-vibe/vibe/"+t+"/track-api/"+e},upvoteTrack:function(t,e){return"/vote/vibe/"+t+"/track/"+e},downvoteTrack:function(t,e){return"/vote/vibe/"+t+"/track/"+e}},getAll:function(){var t=this;return new Promise(function(e,n){return axios.get(t.routes.index).then(function(n){var i=n.data;for(var o in i)i.hasOwnProperty(o)&&t.all.push(i[o].vibe);t.updatePlayingTracksData(),t.updateShowData(),t.loading=!1,e(i)}).catch(function(t){n(t.response.data.errors)})})},create:function(t){var e=this;return t.post(this.routes.create).then(function(t){e.all.push(t.vibe),e.user.updateVibesIDs(t.vibe)}).catch(function(t){return console.log(t)})},get:function(t){var e=this;return new Promise(function(n,i){return axios.get(e.routes.show(t)).then(function(t){n(t.data)}).catch(function(t){i(t.response.data.errors)})})},display:function(t){var e=this;this.showID=parseInt(t),Object.keys(this.all).length>0&&this.all.forEach(function(t){t.id===e.showID&&(e.show=t)})},update:function(t,e){var n=this;return t.update(this.routes.update(e)).then(function(t){n.all=n.all.map(function(e){return e.id===t.vibe.id?(n.show=t.vibe,n.user.updateVibesIDs(t.vibe),t.vibe):e})}).catch(function(t){return console.log(t)})},delete:function(t,e){var n=this;t.delete(this.routes.delete(e)).then(function(t){n.all=n.all.filter(function(t){return t.id!==e}),n.user.vibesIDs=n.user.vibesIDs.filter(function(t){return t!==e}),n.show={},n.deletedMessage=t.message}).catch(function(t){return console.log(t)})},autoRefresh:function(t,e){var n=this;t.post(this.routes.autoRefresh(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},syncVibe:function(t,e){var n=this;t.post(this.routes.syncVibe(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},syncPlaylist:function(t,e){var n=this;t.post(this.routes.syncPlaylist(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},acceptJoinRequest:function(t,e){var n=this;t.delete(this.routes.acceptJoinRequest(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},rejectJoinRequest:function(t,e){var n=this;t.delete(this.routes.rejectJoinRequest(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},sendJoinRequest:function(t,e){var n=this;t.post(this.routes.sendJoinRequest(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},cancelJoinRequest:function(t,e){var n=this;t.delete(this.routes.cancelJoinRequest(e)).then(function(t){n.updateData(t)}).catch(function(t){return console.log(t)})},joinVibe:function(t,e){var n=this;t.post(this.routes.joinVibe(e)).then(function(t){n.updateData(t),n.user.updateVibesIDs(t.vibe)}).catch(function(t){return console.log(t)})},leaveVibe:function(t,e){var n=this;t.delete(this.routes.leaveVibe(e)).then(function(t){n.updateData(t),n.user.vibesIDs=n.user.vibesIDs.filter(function(t){return t!==e})}).catch(function(t){return console.log(t)})},removeUser:function(t,e,n){var i=this;t.delete(this.routes.removeUser(e,n)).then(function(t){i.updateData(t)}).catch(function(t){return console.log(t)})},removeTrack:function(t,e,n){var i=this;t.delete(this.routes.removeTrack(e,n)).then(function(t){i.all=i.all.map(function(e){return i.updateTracksVibesDataForRemovedTrack(e,n,t),e.id===t.vibe.id?t.vibe:e}),i.updateShowData()}).catch(function(t){return console.log(t)})},addTrack:function(t,e,n){var i=this;t.post(this.routes.addTrack(e,n)).then(function(t){i.all=i.all.map(function(e){return i.updateTracksVibesDataForAddedTrack(e,n,t),e.id===t.vibe.id?t.vibe:e}),i.updateShowData()}).catch(function(t){return console.log(t)})},upvoteTrack:function(t,e,n){var i=this;t.post(this.routes.upvoteTrack(e,n)).then(function(t){i.updateData(t)}).catch(function(t){return console.log(t)})},downvoteTrack:function(t,e,n){var i=this;t.delete(this.routes.downvoteTrack(e,n)).then(function(t){i.updateData(t)}).catch(function(t){return console.log(t)})},readyToShow:function(){return Object.keys(this.show).length>0},getVibeName:function(t){if(this.all.length)return this.all.find(function(e){return e.id===t}).name},updateShowData:function(){var t=this;""!==this.showID&&this.all.forEach(function(e){e.id===t.showID&&(t.show=e)})},updatePlayingTracksData:function(){var t={};this.all.forEach(function(e){t[e.id]=""}),this.playingTracks=t},updateData:function(t){var e=this;this.all=this.all.map(function(n){return n.id===t.vibe.id?(e.show=t.vibe,e.message=t.message,setTimeout(function(){return e.message=""},2e4),t.vibe):n})},updateVibeDataForUpdatedVibe:function(t){this.all=this.all.map(function(e){return e.id===t.id?t:e})},updateTracksVibesDataForRemovedTrack:function(t,e,n){t.auto_jd||t.api_tracks.forEach(function(t){if(t.vibon_id===e){var i=t.vibes.indexOf(n.vibe.id);-1!==i&&t.vibes.splice(i,1)}})},updateTracksVibesDataForAddedTrack:function(t,e,n){t.auto_jd||t.api_tracks.forEach(function(t){t.id===e&&t.vibes.push(n.vibe.id)})},isEmpty:function(){return 0===Object.keys(this.all).length}};e.a=i},4:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=n(2),o=n(5),a={vibes:i.a,search:o.a,player:{},show:!1,paused:!1,playingTrack:{},playVibe:function(t){var e=t.playlist_uri,n=t.track_uri,i=t.playerInstance._options,o=i.getOAuthToken,a=i.id;o(function(t){fetch("https://api.spotify.com/v1/me/player/play?device_id="+a,{method:"PUT",body:JSON.stringify({context_uri:e,offset:{uri:n}}),headers:{"Content-Type":"application/json",Authorization:"Bearer "+t}})})},playTracks:function(t){var e=t.tracks_uris,n=t.track_uri,i=t.playerInstance._options,o=i.getOAuthToken,a=i.id;o(function(t){fetch("https://api.spotify.com/v1/me/player/play?device_id="+a,{method:"PUT",body:JSON.stringify({uris:e,offset:{uri:n}}),headers:{"Content-Type":"application/json",Authorization:"Bearer "+t}})})},updateData:function(t){if(t){this.show=!0,this.paused=t.paused,this.playingTrack=t.track_window.current_track;var e=this.playingTrack.linked_from.id?this.playingTrack.linked_from.id:this.playingTrack.id,n=t.context.uri;null===n?this.updateSearchPlayingTracks(e):this.updateVibePlayingTracks(e,n)}},updateSearchPlayingTracks:function(t){this.search.playingTrack=t},updateVibePlayingTracks:function(t,e){var n=this;Object.keys(this.vibes.all).length>0&&this.vibes.all.map(function(i){i.uri===e&&(i.api_tracks.forEach(function(e){e.id===t&&(n.vibes.playingTracks[i.id]=e.id)}),n.vibes.playingID=i.id)})},playOrResume:function(){this.player.resume().then(function(){})},pause:function(){this.player.pause().then(function(){})},previous:function(){this.player.previousTrack().then(function(){})},next:function(){this.player.nextTrack().then(function(){})}};window.playback=a,e.default=a},5:function(t,e,n){"use strict";var i={route:"",tracks:{},playingTrack:"",setRoute:function(t){this.route="/search/"+t},searchInput:function(t){var e=this;return this.setRoute(t),new Promise(function(t,n){axios.get(e.route).then(function(n){e.tracks=n.data,t(n.data)}).catch(function(t){n(t.response.data.errors)})})}};e.a=i},86:function(t,e,n){t.exports=n(4)}});