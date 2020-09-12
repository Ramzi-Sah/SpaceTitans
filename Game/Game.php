<!DOCTYPE HTML>
<!--
- Space Titans v2.0 -
© Copyright Ramzi, 2018 - 2666 All Right Reserved.

Not liable for any damage incurred from use of this software
including but not limited to : temporary paralysis, spontaneous combustion and premature hair loss.
-->
<html>
	<head>
		<title>Space Titans</title>
		<link rel="stylesheet" href="..\css\Game.css" />
	</head>
	
	<body>
		<div id="ActionBar">
			<button onclick="window.location.href='/'">Main Page</button>
			
			<button onclick="window.location.href='DashBoard.php'">Back DashBoard</button>
		</div>
		<nav id="LeftBar">
			<label>Options: </label><br><br>
			<label>Enable Somting</label><button>click</button>
		</nav>
		<div id="GameLayout">
			<canvas id="ctx" width="650" height="650"></canvas>
		</div>
		
		<script>
			var ctx = document.getElementById("ctx").getContext("2d"); 
			
			var ctx_HEIGHT = document.getElementById("ctx").width;
			var ctx_WIDTH = document.getElementById("ctx").height;
			
			var timeWhenGameStarted = Date.now(); //ms
			/*-------------- Game Params -------------*/
				var SpawnMeteor = 10000; // ms time to wait for blackholes to spawn new meteor (can cause lag if bellow 5 Seconds)
				var AdvencedLogs = false;
			/*---------------------------*/
//---------------------------------------- Game Objects ---------------------------------------------//	
		//-------------------World----------------------//
			var WorldSizeX = 50000;
			var WorldSizeY = 50000;
			var world = {
				World_x:0,
				World_y:0,
			}
		//-------------------player----------------------//
			var PlayerShip = {
				obj:"player",
				x: ctx_WIDTH/2 - 24,
				y: ctx_HEIGHT/2 - 24,
				World_x: world.World_x +8100,
				World_y: world.World_y + 25000,
				img:'../assets/SpaceShips/Core/Core_1.png',
				name:'',
				SizeX:48,
				SizeY:48,
				spdX:0,
				spdY:0,
				spdMax:5,
				LifeBar:false,
				Acceleration:0.5,
				hp:100
			};
			document.onkeyup = function(e) {
				switch (e.keyCode) {
					case 100: //left
						PlayerShip.spdX -= PlayerShip.Acceleration; 
						break;
					case 104: //up
						PlayerShip.spdY -= PlayerShip.Acceleration; 
						break;
					case 102: //right
						PlayerShip.spdX += PlayerShip.Acceleration; 
						break;
					case 98: //down
						PlayerShip.spdY += PlayerShip.Acceleration; 
						break;
					case 101: //Num 5
						CreateNewBullet();
						break;
					case 99: //Num 3
						 // CreateNewMeteor_OnPos(8000,25000);
						break;
					default :
						// console.log("Key " + e.key + " presseed, code : (" + e.keyCode + ")");
					break;
				}
			};
			
			function PlayerMove(){
				if(PlayerShip.spdX > PlayerShip.spdMax){
					PlayerShip.spdX = PlayerShip.spdMax;
				}
				if(PlayerShip.spdY > PlayerShip.spdMax){
					PlayerShip.spdY = PlayerShip.spdMax;
				}
				if(PlayerShip.spdX < -PlayerShip.spdMax){
					PlayerShip.spdX = -PlayerShip.spdMax;
				}
				if(PlayerShip.spdY < -PlayerShip.spdMax){
					PlayerShip.spdY = -PlayerShip.spdMax;
				}
				PlayerShip.World_x += PlayerShip.spdX;
				PlayerShip.World_y += PlayerShip.spdY;
			}
			
			//getting mouse input
			var mouseX;
			var mouseY;
			var mouse_World_X;
			var mouse_World_Y;
			
			document.onmousemove = function(mouse){
				mouseX = mouse.clientX - ctx.canvas.offsetLeft + document.body.scrollLeft;
				mouseY = mouse.clientY - ctx.canvas.offsetTop + document.body.scrollTop;
				
				mouse_World_X = mouse.clientX + PlayerShip.World_x;
				mouse_World_Y = mouse.clientY + PlayerShip.World_y;
			};
			//-------------------Bullet----------------------//
			var BulletList = {};
			Bullet = function (id){
				var Bullet = {
					id:id,
					obj:"Bullet",
					alive:true,
					x: -ctx_WIDTH/2 + 10,
					y: -ctx_HEIGHT/2 + 10,
					World_x:PlayerShip.World_x,
					World_y:PlayerShip.World_y,
					img:'../assets/Bullet.png',
					SizeX:20,
					SizeY:20,
					spdX:15,
					spdY:0,
					spdmax:8,
					accX:0.5,
					accY:0.5,
					dega:25,
					LifeBar:false,
					TravelDist:2000 // distance from player that the bullet can travel (the bullet will be deleted) 
				};
				BulletList[id] = Bullet;
			}
			
			function CreateNewBullet(){
				Bullet('Bullet_' + Math.floor(Math.random() * 2000));
			}
			
			function BulletForce(Bullet){
				// console.log(Bullet.id);
				
				// if (Target.World_x > Bullet.World_x){ 
					// Bullet.spdX += Bullet.accX;
					// if (Bullet.World_x > (Target.World_x + 2050)){
						// Bullet.World_x = 2050;
					// };
				// };
				
				if (Bullet.World_x > (PlayerShip.World_x + Bullet.TravelDist) || Bullet.World_y > (PlayerShip.World_y + Bullet.TravelDist) ){
					Bullet.alive = false;
				}
				if(Bullet.alive){
					Bullet.World_x += Bullet.spdX;
					Bullet.World_y += Bullet.spdY;
				}
			}
			
		//------------------meteor-----------------------//
			var meteorList = {};
			Meteor = function (id,newWorld_x,newWorld_y,spdX,spdY,Size){
				if (Size == 1){
						var Meteor = { // small meteor
							id:id,
							obj:"meteor",
							alive:true,
							x:-ctx_WIDTH/2+25,
							y:-ctx_HEIGHT/2+25,
							World_x:newWorld_x,
							World_y:newWorld_y,
							img:'../assets/meteor.png',
							spdX:spdX,
							spdY:spdY,
							SizeX:25,
							SizeY:25,
							hp:100,
							LifeBar:true,
							TravelDist:20000 // distance from player that the bullet can travel (the bullet will be deleted) 
						};
					}else if (Size == 2){
						var Meteor = { // medium meteor
							id:id,
							obj:"meteor",
							alive:true,
							x:-ctx_WIDTH/2+25,
							y:-ctx_HEIGHT/2+25,
							World_x:newWorld_x,
							World_y:newWorld_y,
							img:'../assets/meteor.png',
							spdX:spdX,
							spdY:spdY,
							SizeX:50,
							SizeY:50,
							hp:100,
							LifeBar:true,
							TravelDist:20000 // distance from player that the bullet can travel (the bullet will be deleted) 
						};
					}else if (Size == 3){
						var Meteor = { // Big meteor
							id:id,
							obj:"meteor",
							alive:true,
							x:-ctx_WIDTH/2+25,
							y:-ctx_HEIGHT/2+25,
							World_x:newWorld_x,
							World_y:newWorld_y,
							img:'../assets/meteor.png',
							spdX:spdX,
							spdY:spdY,
							SizeX:96,
							SizeY:96,
							hp:100,
							LifeBar:true,
							TravelDist:20000 // distance from player that the bullet can travel (the bullet will be deleted) 
						};
					}else {
						var Meteor = { // default meteor values
							id:id,
							obj:"meteor",
							alive:true,
							x:-ctx_WIDTH/2+25,
							y:-ctx_HEIGHT/2+25,
							World_x:newWorld_x,
							World_y:newWorld_y,
							img:'../assets/meteor.png',
							spdX:spdX,
							spdY:spdY,
							SizeX:50,
							SizeY:50,
							hp:100,
							LifeBar:true,
							TravelDist:20000 // distance from player that the bullet can travel (the bullet will be deleted) 
						};
					}
				
				meteorList[id] = Meteor;
			}
			
			function CreateNewMeteor_OnPos(pos_word_X,pos_Word_Y){
				var random = (Math.floor(Math.random() * 20));
				if(random < 5){
					Meteor('Meteor_' + Math.floor(Math.random() * 2000),pos_word_X,pos_Word_Y,-Math.floor(Math.random() * 5),-Math.floor(Math.random() * 5),Math.floor(Math.random() * 3));
				}if(random > 5 && random < 10){
					Meteor('Meteor_' + Math.floor(Math.random() * 2000),pos_word_X,pos_Word_Y,-Math.floor(Math.random() * 5),Math.floor(Math.random() * 5),Math.floor(Math.random() * 3));
				}if(random > 10 && random < 15){
					Meteor('Meteor_' + Math.floor(Math.random() * 2000),pos_word_X,pos_Word_Y,Math.floor(Math.random() * 5),-Math.floor(Math.random() * 5),Math.floor(Math.random() * 3));
				}else {
					Meteor('Meteor_' + Math.floor(Math.random() * 2000),pos_word_X,pos_Word_Y,Math.floor(Math.random() * 5),Math.floor(Math.random() * 5),Math.floor(Math.random() * 3));
				}
			}
			
			function MeteorMove(Meteor){
			
				if (Meteor.World_x > (PlayerShip.World_x + Meteor.TravelDist) || Meteor.World_y > (PlayerShip.World_y + Meteor.TravelDist) || Meteor.World_y <(PlayerShip.World_y - Meteor.TravelDist) || Meteor.World_x < (PlayerShip.World_x - Meteor.TravelDist)){
					Meteor.alive = false;
				}
				Meteor.World_x += Meteor.spdX;
				Meteor.World_y += Meteor.spdY;
			}
			function DestroyMeteor(Meteor){
				Meteor.alive = false;
			}
		//------------------- Black Holes ---------------//
			var BlackHole_1 = {
				obj:"BlackHole",
				x:-ctx_WIDTH/2+256,
				y:-ctx_HEIGHT/2+256,
				World_x:2000,
				World_y:10000,
				img:'../assets/Planets/BlackHole.png',
				spdY:0,
				spdX:0,
				SizeX:512,
				SizeY:512,
				gravity:500000,
				Last_BlackHole:false,// used for the SpawnMeteors Delay (reset time)
				LifeBar:false
			};
			var BlackHole_2 = {
				obj:"BlackHole",
				x:-ctx_WIDTH/2+256,
				y:-ctx_HEIGHT/2+256,
				World_x:10000,
				World_y:30000,
				img:'../assets/Planets/BlackHole.png',
				spdY:0,
				spdX:0,
				SizeX:512,
				SizeY:512,
				gravity:500000,
				Last_BlackHole:true,// used for the SpawnMeteors Delay (reset time)
				LifeBar:false
			};
			var wait = Date.now();
			function SpawnMeteors(BlackHole){
				var time = Date.now();
				// console.log(BlackHole);
				if (time >= wait + SpawnMeteor){
					CreateNewMeteor_OnPos(BlackHole.World_x,BlackHole.World_y);
					if(BlackHole.Last_BlackHole){
						wait = Date.now();
					}
				}
			}
			
		//-------------------planets----------------------//
			//earth
			var earth = {
					obj:"planet",
					x:-ctx_WIDTH/2+256,
					y:-ctx_HEIGHT/2+256,
					World_x:8500,
					World_y:25000,
					img:'../assets/Planets/earth.png',
					spdY:0.15,
					spdX:0,
					SizeX:512,
					SizeY:512,
					gravity:50,
					LifeBar:false,
					hp:100
				};
			//mars
			var mars = {
					obj:"planet",
					x:-ctx_WIDTH/2+256,
					y:-ctx_HEIGHT/2+256,
					World_x:2000,
					World_y:15000,
					img:'../assets/Planets/mars.png',
					spdY:0.1,
					spdX:0,
					SizeX:512,
					SizeY:512,
					gravity:35,
					LifeBar:false,
					hp:100
				};
				
			function PlanetsMove(planet){
				planet.World_x += planet.spdX;
				planet.World_y += planet.spdY;
			}
//------------------------------------------------------------------------------------------------------------------//				
//--------------------------------------------- Game Manager ---------------------------------------------------------------//	
//------------------------------------------------------------------------------------------------------------------//	
			updateEntityPosition = function(something){
			
				if(something == PlayerShip){
					PlayerMove();
					for(var key in meteorList){
						// console.log(meteorList[key].id);
						console.log();
						if(meteorList[key].alive){
							var dist_meteor_player = getDistanceBetweenEntity(PlayerShip,meteorList[key]);
							if (dist_meteor_player < ((something.SizeX/2)+(meteorList[key].SizeX/2))){
								alert("Colision avec le meteor!");
							}
						}
					};		
				}else if(something.obj == "meteor"){
					MeteorMove(something);
					for(var key in meteorList){
						if(meteorList[key].alive){
							if (something != meteorList[key]){
								var dist_bullet_meteor = getDistanceBetweenEntity(something,meteorList[key]);
								if (dist_bullet_meteor < ((something.SizeX/2)+(meteorList[key].SizeX/2))){
									if(AdvencedLogs){
										console.log("Collision meteor("+ something.id +") and meteor (" + meteorList[key].id + ")");
									}
									something.alive = false;
									meteorList[key].alive = false;
								}
							}
						}
					};
					if (something.hp <= 0) {
						DestroyMeteor(something);
					}
				}else if(something.obj == "planet"){
					PlanetsMove(something);
				}else if(something.obj == "BlackHole"){
					SpawnMeteors(something);
				}else if(something.obj == "Bullet"){
					BulletForce(something);
					//check collision with meteor
					for(var key in meteorList){
						// console.log(meteorList[key].id);
						if(meteorList[key].alive){
							var dist_bullet_meteor = getDistanceBetweenEntity(something,meteorList[key]);
							if (dist_bullet_meteor < ((something.SizeX/2)+(meteorList[key].SizeX/2))){
								if(AdvencedLogs){
									console.log("Collision bullet("+ something.id +") - meteor (" + meteorList[key].id + ")");
								}
								something.alive = false;
								meteorList[key].hp -= something.dega;
							}
						}
					};	
				}
			
				// Calculate new position when player move (move with player)
				if (something != PlayerShip){
					if (PlayerShip.spdX != 0){
						something.World_x -= PlayerShip.spdX;
					}
					if (PlayerShip.spdY != 0){
						something.World_y -= PlayerShip.spdY;
					}
				}
				// test if out of World
				if (something.World_x >= WorldSizeX){
					something.World_x = WorldSizeX;
				}
				if (something.World_y >= WorldSizeY){
					something.World_y = WorldSizeY;
				}
				if (something.World_x < 0){
					something.World_x = 0;
				}
				if (something.World_y < 0){
					something.World_y = 0;
				}
				
			}

			drawEntity = function(something){
				var imgEntity = new Image();
				imgEntity.src = something.img;
				if (something == PlayerShip){
					ctx.drawImage(imgEntity,something.x,something.y,something.SizeX,something.SizeY);
				}else{
					ctx.drawImage(imgEntity,something.World_x - something.x - PlayerShip.World_x , something.World_y  - something.y - PlayerShip.World_y  ,something.SizeX,something.SizeY);				
				}
			}
			function DrawLifeBar(something){
				if (something.LifeBar){
					var ShowLifeBar = false;
					if(something.hp != 100 ){ ShowLifeBar = true;};
					if(ShowLifeBar){
						ctx.fillStyle = '#000';
						ctx.fillRect(something.World_x - something.x - PlayerShip.World_x- something.SizeX *0.5 -1,something.World_y  - something.y - PlayerShip.World_y+something.SizeX*1.5 -1,something.hp+2,10);
						if (something.hp >= 100){
							ctx.fillStyle = '#00FF00';
						}else if ( something.hp < 100 && something.hp >= 60){
							ctx.fillStyle = 'green';
						}else if (something.hp < 60 && something.hp > 25){
							ctx.fillStyle = 'yellow';
						}else if (something.hp <= 25){
							ctx.fillStyle = 'red';
						}else{
							ctx.fillStyle = 'white';
						}
						ctx.fillRect(something.World_x - something.x - PlayerShip.World_x-something.SizeX *0.5,something.World_y  - something.y - PlayerShip.World_y+something.SizeX*1.5,something.hp,8);
					}
					
				}
			}
			
			function updateEntity(something){
				updateEntityPosition(something);
				drawEntity(something);
			}
			
			/* -------------------------draw img function with degres------------------------------*/
			function drawImageRot(img,x,y,width,height,deg){
				//Convert degrees to radian 
				var rad = deg * Math.PI / 180;
				//Set the origin to the center of the image
				ctx.translate(x + width / 2, y + height / 2);
				//Rotate the canvas around the origin
				ctx.rotate(rad);
				//draw the image    
				ctx.drawImage(img,width / 2 * (-1),height / 2 * (-1),width,height);
				//reset the canvas  
				ctx.rotate(rad * ( -1 ) );
				ctx.translate((x + width / 2) * (-1), (y + height / 2) * (-1));
			}
		/*------------------------------------------------------------------------*/
			
			//distance
			function getDistanceBetweenEntity(entity1,entity2){	
				var vx = entity1.World_x - entity2.World_x;
				var vy = entity1.World_y - entity2.World_y;
				return Math.sqrt(vx*vx+vy*vy);
			}
//-------------------------------------------------------------------------------------//		
			
//-------------------------------------- UI -----------------------------------------------//	
			var Radar = {
				x:30,
				y:ctx_HEIGHT-125,
				RadarMaxX:178,
				RadarMaxY:ctx_HEIGHT-200,
				RadarMinX:30,
				RadarMinY:ctx_HEIGHT-130
			}
			
			function Radar_get_pos (Target){
				Radar.x = (Target.World_x - PlayerShip.World_x)/(Radar.x * 3) + 100;
				Radar.y = (Target.World_y - PlayerShip.World_y)/(Radar.y * 0.3) + Radar.RadarMinY;
				// check if out of radar sensors
				if (Radar.x > Radar.RadarMaxX || Radar.x < Radar.RadarMinX || Radar.y > Radar.RadarMaxY || Radar.y < Radar.RadarMinY){
					// Radar.x = -9999;
					// Radar.y = 9999;
				}
			}
			
			function Draw_Radar (){
				//Radar
					//-------------------------------------//
					//Radar Background
					ctx.fillStyle = '#73c6a1';
					ctx.fillRect(30,ctx_HEIGHT-200,150,150);
					//Player Ship
					ctx.fillStyle = 'white';
					ctx.fillRect(30,ctx_HEIGHT-130,150,0.5);
					ctx.fillRect(100,ctx_HEIGHT-200,0.5,150);
					//--------------------------------------//
					ctx.fillStyle = '#7c8d9a';
					ctx.fillRect(30,ctx_HEIGHT-200,150,2); // up right
					ctx.fillRect(30,ctx_HEIGHT-50,2,-150); // bottom right
					ctx.fillRect(178,ctx_HEIGHT-200,2,150); //up left
					ctx.fillRect(180,ctx_HEIGHT-50,-150,2); // bottom left
					//--------------------------------------//
					//meteor
					for(var key in meteorList){
						if(meteorList[key].alive){
							ctx.fillStyle = 'orange';
							Radar_get_pos(meteorList[key]);
							ctx.fillRect(Radar.x-1,Radar.y-1,2,2);
						}
					};
					/*----------------planets-----------------*/
					//earth
					ctx.fillStyle = 'blue';
					Radar_get_pos(earth);
					ctx.fillRect(Radar.x-3,Radar.y-3,6,6);
					//mars
					ctx.fillStyle = 'red';
					Radar_get_pos(mars);
					ctx.fillRect(Radar.x-3,Radar.y-3,6,6);
					/*----------------blackholes--------------*/
					ctx.fillStyle = 'white';
					Radar_get_pos(BlackHole_1);
					ctx.fillRect(Radar.x-3,Radar.y-3,6,6);
					//
					Radar_get_pos(BlackHole_2);
					ctx.fillRect(Radar.x-3,Radar.y-3,6,6);
					/*---------------------------------------*/
					
					//Bulles
					// for(var key in BulletList){
						// ctx.fillStyle = 'white';
						// Radar_get_pos(BulletList[key]);
						// ctx.fillRect(Radar.x-2.5,Radar.y-2.5,5,5);
					// };
					
			}
			
			function DrawGrid(){
				ctx.fillStyle = 'white';
				ctx.fillRect( ctx_WIDTH /3, PlayerShip.y  - ctx_WIDTH/2 ,0.3,9999);
				ctx.fillRect( 2 * ctx_WIDTH /3, PlayerShip.y  - ctx_WIDTH/2 ,0.3,9999);
				
				ctx.fillRect(PlayerShip.x - ctx_WIDTH/2, ctx_WIDTH /3 ,9999,0.3);
				ctx.fillRect(PlayerShip.x - ctx_WIDTH/2, 2*ctx_WIDTH /3 ,9999,0.3);
			}
			
			function DrawUI(){
				ctx.fillStyle = 'white';
				ctx.fillText("Speed X : " +  PlayerShip.spdX ,2,20);
				ctx.fillText("Speed Y : " +  PlayerShip.spdY ,2,40);
				
				ctx.fillText("player world x : " + PlayerShip.World_x ,ctx_WIDTH-200,ctx_HEIGHT-ctx_HEIGHT/15);
				ctx.fillText("player world y : " + PlayerShip.World_y ,ctx_WIDTH-200,ctx_HEIGHT-ctx_HEIGHT/15 + 20);
				
				//mouse information
				ctx.fillStyle = 'yellow';
				ctx.font = '12px Arial';
				ctx.fillText("X : " + mouseX ,mouseX,mouseY+20);
				ctx.fillText("Y : " + mouseY ,mouseX,mouseY+35);
				ctx.fillText("World X : " + mouse_World_X,mouseX,mouseY+55);
				ctx.fillText("World Y : " + mouse_World_Y,mouseX,mouseY+70);
				// ctx.fillText("debug : " + mouse_World_X,mouseX,mouseY+55);
				
				//Radar
				Draw_Radar ();
			
				// ctx.fillRect(PlayerShip.x +24,PlayerShip.y +24 ,mouseX - ctx_WIDTH/2 - 250 ,mouseY - ctx_WIDTH/2);

				//default styles
				ctx.fillStyle = 'white';
				ctx.font = '18px Arial';
			}
//--------------------------------------Debug-----------------------------------------------//	
			// console.log();
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------ UPDATE FUNCTION ------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
			update = function(){
				// console.log(document.body.scrollTop);
				ctx.clearRect(0,0,ctx_HEIGHT,ctx_WIDTH);
				DrawGrid();
				
				//=> to do (for Each loop -on planets array-)
				// planets need to be the last layer
				updateEntity(earth);
				updateEntity(mars);
				updateEntity(BlackHole_1);
				updateEntity(BlackHole_2);
				/*-------------------------------------------*/
				
				// Bullets
				for(var key in BulletList){
					// console.log(BulletList[key]);
					if(BulletList[key].alive){
						updateEntity (BulletList[key]);
					}
				};
				
				updateEntity(PlayerShip);
				
				// meteor
				for(var key in meteorList){
					// console.log(meteorList[key].id);
					if(meteorList[key].alive){
						updateEntity (meteorList[key]);
						DrawLifeBar (meteorList[key]);
					}
				};
				
				DrawUI();
			}
			setInterval(update,20);
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------ End ------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------
		function btn_Up(){
			PlayerShip.spdY -= PlayerShip.Acceleration; 
		}
		function btn_Down(){
			PlayerShip.spdY += PlayerShip.Acceleration; 
		}
		function btn_Right(){
			PlayerShip.spdX -= PlayerShip.Acceleration;
		}
		function btn_Left(){
			PlayerShip.spdX += PlayerShip.Acceleration; 
		}
		function btn_Fire(){
			CreateNewBullet();
		}
		</script>
  
		<div id="Footer">
			<button onclick="btn_Up();">˄</button><br><br>
			<button onclick="btn_Right();">˂</button>
			<button onclick="btn_Down();">˅</button>
			<button onclick="btn_Left();">˃</button><br><br>
			<button onclick="btn_Fire();">Fire</button><br><br>
		</div>
  </body >
</html>