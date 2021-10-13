

//PIXI = require('pixi.js');
import * as PIXI from 'pixi.js'
const Viewport = require('pixi-viewport').Viewport
const Grid = require('./Grid').Grid
import Swup from 'swup'

const swup = new Swup()
swup.on('contentReplaced', destroy);

console.log(swup)


let view = document.querySelector('.view')
// VARIABLE
let width, height, viewport, app

let arrTrans = JSON.parse(document.getElementById('pageData').innerHTML);


function destroy(){
  app.destroy()
  arrTrans = JSON.parse(document.getElementById('pageData').innerHTML);
  init()
}


function initApp(){

  app = new PIXI.Application({view})

  app.backgroundColor =0x000000
  app.renderer.autoDensity = true
  app.renderer.resize(window.innerWidth, window.innerHeight)
  app.renderer.backgroundColor = 0xFFFFFF;
  

  
}

let gridSize = 70
const gridMin = 9
const imagePadding = 125
let gridColumnsCount, gridRowsCount, gridColumns, gridRows, grid, container, rects, images,imagesUrls, text
const centralText='FAF Carrelage'

function init (){
  initApp()
  initDimensions ()
  initViewport()
  initContainer()
  
  initGrid()
  initText()
  

  initRectsAndImages()
  fitWorld()

  app.ticker.add(() => {
    checkRectsAndImages()
    checkText()  
  })
  debug()
}

function initDimensions (){
  width = window.innerWidth
  height = window.innerHeight
}

function initViewport(){ 
  viewport = new Viewport({
    screenWidth: width,
    screenHeight: height,
    interaction: app.renderer.plugins.interaction 
  })
  viewport.cursor ='move'

  app.stage.addChild(viewport)

  viewport
    .drag()
    .pinch()
    .wheel()
    .decelerate()
    //
    .clampZoom({maxScale: 0.7,  minScale :0.2 })
    .on('clicked', click)
    //.bounce({time:400});
  

}
function click(e){
  //console.log(e)
}

window.onload = init
window.addEventListener('resize', onResize)


let resizeTimer

function onResize () {
  
  if (resizeTimer) clearTimeout(resizeTimer)
  resizeTimer = setTimeout(() => {
    app.ticker.stop()
    init()
  }, 100)  
}

function initGrid () {

  gridColumnsCount = Math.ceil(width / gridSize)
  gridRowsCount = Math.ceil(height / gridSize)
  gridColumns = gridColumnsCount
  gridRows = gridRowsCount


  gridColumns = gridColumnsCount
  gridRows = gridRowsCount

  let i = 1.0;
  while ( gridRows*gridColumns < arrTrans.length*gridMin*gridMin*2.5) {
    gridColumns =gridColumnsCount* i
    gridRows = gridRowsCount*i
    i+=0.01;
  }

  grid = new Grid(gridSize, gridColumns, gridRows, gridMin)
  rects = grid.generateRects(arrTrans.length)
  images =[]
  imagesUrls = {}
}

function initRectsAndImages () {
  const graphics = new PIXI.Graphics()
  //graphics.beginFill(0xFFFFFF)
  rects.forEach(rect => {
    const image = new PIXI.Sprite()
    image.x = rect.x * gridSize
    image.y = rect.y * gridSize
    image.width = rect.w * gridSize - imagePadding
    image.height = rect.h * gridSize - imagePadding
    // Set it's alpha to 0, so it is not visible initially
    image.alpha = 0
    images.push(image)

    graphics.drawRect(image.x, image.y, image.width, image.height)
    

  })
 
  // Ends the fill action
  graphics.endFill()
  // Add the graphics (with all drawn rects) to the container
  container.addChild(graphics)
  // Add all image's Sprites to the container
  images.forEach(image => {
    container.addChild(image)

    // Opt-in to interactivity
   image.interactive = true;

   // Shows hand cursor
   image.buttonMode = true;

   image.cursor='pointer'
  })
}




function fitWorld(){
  //viewport.worldHeight = container.height;
  //viewport.worldWidth = container.width;
  //
  viewport.bounce({})
  viewport.moveCenter(viewport.worldWidth/2,viewport.worldHeight/2)
  viewport.scaled=0.2
  viewport.animate({
    time: 4000, 
    scale: 0.35,
    ease: 'easeInOutSine',
  })
  
}

function initContainer () {
  container = new PIXI.Container()
  container.sortableChildren = true
  viewport.addChild(container)
  container.cursor='move'
}

function checkRectsAndImages(){
  
  images.forEach((image, index) => {

    // Check if the rect intersects with the viewport
    if (rectIntersectsWithViewport(image)) {
      
      // If rect just has been discovered
      // start loading image
      if (!image.discovered) {
        image.discovered = true
        setTimeout(function() {
   
          loadTextureForImage(index)
        },Math.floor(Math.random() * (500 - 10)) + 10);
      }
      // If image is loaded, increase alpha if possible
      if (image.loaded && image.alpha < 1) {
        image.alpha += 0.01
      }
    }
  })
}

function rectIntersectsWithViewport (image) {

  return (
    
    image.x + image.width > viewport.hitArea.x &&
    image.x < viewport.hitArea.x + viewport.hitArea.width &&
    image.y + image.height > viewport.hitArea.y &&
    image.y < viewport.hitArea.y + viewport.hitArea.height
  )
}

function loadTextureForImage (index) {
  // Get image Sprite
  const image = images[index]
  // Set the url to get a random image from Unsplash Source, given image dimensions
  const url = arrTrans[index]
  // Get the corresponding rect, to store more data needed (it is a normal Object)
  const rect = rects[index]
  // Create a new AbortController, to abort fetch if needed
  const { signal } = rect.controller = new AbortController()
  // Fetch the image
  const loader = new PIXI.Loader();
    // Get image URL, and if it was downloaded before, load another image
    // Otherwise, save image URL and set the texture
    const id = url
   
    if (imagesUrls[id]) {
      setTimeout(function() {
   
        loadTextureForImage(index++)
      },Math.floor(Math.random() * (500 - 10)) + 10);
      
    } else {
      loader.add(id)
      loader.load((loader, resources) => {
        
        imagesUrls[id] = true
        let texture = PIXI.Texture.from(url)
        image.texture = cropImage(image,texture) 
        //image.texture = texture

        image.loaded = true
      })
    }

}

function initText(){
  text = new PIXI.Text(centralText.slice(0,1),{fontSize:118, fontFamily:'Stardos Stencil',align:'center'});
  container.addChild(text);
  for (let i=0; i< centralText.length; i++){
    textDelayDisplay(i)
  }
}

function textDelayDisplay(i){
  setTimeout(function() {
    text.text=centralText.slice(0,1+i)
  }, i*100);
}

function checkText(){

  text.x = viewport.hitArea.x + viewport.hitArea.width /2 -text.width/2;
  text.y = viewport.hitArea.y + viewport.hitArea.height /2 -text.height/2;
}
function cropImage(image,texture){
  return texture
  let imageRatioW = image.width/image.height

  let textureRatio = texture.width/texture.height
  
  let texture2
  let ratioW = texture.width /image.width
  let ratioH = texture.height /image.height

 
  if (ratioW > ratioH){
    let wcut = (texture.width-image.width)/2


    texture2 = new PIXI.Texture(texture,new PIXI.Rectangle(wcut, 0, image.width, texture.height))
    //texture2=texture
  }
  else{
    let hcut = (texture.height-image.height)/2
     texture2 = new PIXI.Texture(texture,new PIXI.Rectangle(0, hcut, texture.width, image.height))
     //texture2=texture
  //  
  }
  console.log(texture2.width)

  return texture
}
function debug(){
  
  console.log(viewport)
}