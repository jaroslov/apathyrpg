#!/usr/bin/env python2.5
import curses
from curses import wrapper as cwrap

class Apathy(object):
  def __init__(self):
    self.Cursor = [0,0]
    self.Screen = None
  def interpretCommands(self,ShowCursorPosition=False):
    CommandChar = self.Screen.getch()
    Color = curses.COLOR_BLUE
    if CommandChar in [curses.KEY_DOWN,66]:
      self.Cursor[0]+=1
      Color = curses.COLOR_BLUE
    elif CommandChar in [curses.KEY_UP,65]:
      self.Cursor[0]-=1
      Color = curses.COLOR_GREEN
    elif CommandChar in [curses.KEY_RIGHT,67]:
      self.Cursor[1]+=1
      Color = curses.COLOR_MAGENTA
    elif CommandChar in [curses.KEY_LEFT,68]:
      self.Cursor[1]-=1
      Color = curses.COLOR_RED
    else:
      self.Screen.addstr(2,1,str(CommandChar))
    if ShowCursorPosition:
      self.Screen.addstr(1,1,str(self.Cursor),curses.A_BOLD)
      self.Screen.addstr(3,1,str(curses.COLOR_BLUE|curses.A_BOLD),curses.COLOR_BLUE)

  def __call__(self,stdscr):
    self.Screen = stdscr
    self.Screen.keypad(1)
    self.Screen
    while True:
      self.Screen.border()
      oldCursor = self.Cursor[:]
      self.interpretCommands(True)
      try:
        self.Screen.move(self.Cursor[0],self.Cursor[1])
      except:
        self.Cursor = oldCursor
      self.Screen.refresh()

def apathy():
  cwrap(Apathy())

if __name__=="__main__":
  apathy()