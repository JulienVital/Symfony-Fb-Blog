
export class Grid {

    // The constructor receives all the following parameters:
    // - gridSize: The size (width and height) for smallest unit size
    // - gridColumns: Number of columns for the grid (width = (gridColumns * gridSize)*5)
    // - gridRows: Number of rows for the grid (height = gridRows * gridSize)
    // - gridMin: Min width and height limits for rectangles (in grid units)
    constructor(gridSize, gridColumns, gridRows, gridMin) {
      
      this.gridSize = gridSize
      this.gridColumns = gridColumns
      this.gridRows = gridRows
      this.gridMin = gridMin
      this.rects = []
      this.currentRects = [{ x: 0, y: 0, w: this.gridColumns, h: this.gridRows }]
    }

    splitCurrentRect (picturesCount) {
      
      if (this.rects.length + this.currentRects.length==picturesCount){

        while (this.currentRects.length) {
          const currentRect = this.currentRects.shift()
          this.rects.push(currentRect)
        }
        
      }

      if (this.currentRects.length) {
        const currentRect = this.currentRects.shift()
        const cutVertical = currentRect.w > currentRect.h
        const cutSide = cutVertical ? currentRect.w : currentRect.h
        const cutSize = cutVertical ? 'w' : 'h'
        const cutAxis = cutVertical ? 'x' : 'y'

        
        if (cutSide > this.gridMin * 2 ) {
          const rect1Size = randomInRange(this.gridMin, cutSide - this.gridMin)
          const rect1 = Object.assign({}, currentRect, { [cutSize]: rect1Size })
          const rect2 = Object.assign({}, currentRect, { [cutAxis]: currentRect[cutAxis] + rect1Size, [cutSize]: currentRect[cutSize] - rect1Size })
          this.currentRects.push(rect1, rect2)
        }
        else {
          this.rects.push(currentRect)

          this.splitCurrentRect()
        }
      }
    }

    generateRects (picturesCount) {

      while (this.currentRects.length  ) {
        this.splitCurrentRect(picturesCount)
      }
      return this.rects
    }
  }
  
  function randomInRange (min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min
  }