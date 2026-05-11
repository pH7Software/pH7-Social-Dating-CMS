"use client";
import { useState } from "react";
import { AppNav } from "@/components/Navbar";
import { Search, Send, Phone, Video, MoreVertical, MapPin } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

const conversations = [
  { id: 1, name: "Elena", age: 24, color: "#7c3aed", online: true, lastMsg: "Hey! I saw you like hiking too 🏔️", time: "2m", unread: 2 },
  { id: 2, name: "Sophia", age: 26, color: "#db2777", online: false, lastMsg: "Would love to grab coffee sometime!", time: "1h", unread: 0 },
  { id: 3, name: "Maya", age: 23, color: "#059669", online: true, lastMsg: "What kind of music are you into?", time: "3h", unread: 1 },
  { id: 4, name: "Rachel", age: 28, color: "#d97706", online: true, lastMsg: "Haha that's so funny 😂", time: "1d", unread: 0 },
  { id: 5, name: "Jessica", age: 25, color: "#2563eb", online: false, lastMsg: "We should check out that gallery", time: "2d", unread: 0 },
];

const chatMessages = [
  { id: 1, from: "them", text: "Hey! I noticed you like hiking 🏔️", time: "10:32 AM" },
  { id: 2, from: "me", text: "Yes! I go every weekend. Do you have a favorite trail?", time: "10:34 AM" },
  { id: 3, from: "them", text: "I love the Appalachian Trail! I did a section last summer. You should come next time!", time: "10:35 AM" },
  { id: 4, from: "me", text: "That sounds amazing! I've always wanted to do a multi-day hike.", time: "10:38 AM" },
  { id: 5, from: "them", text: "It's life-changing 😄 What's your photography style? I saw some of your shots!", time: "10:40 AM" },
  { id: 6, from: "me", text: "Mostly landscape and street photography. I love capturing candid moments.", time: "10:42 AM" },
  { id: 7, from: "them", text: "Hey! I saw you like hiking too 🏔️", time: "10:45 AM" },
];

export default function MessagesPage() {
  const [active, setActive] = useState(conversations[0]);
  const [message, setMessage] = useState("");

  return (
    <div className="min-h-screen bg-background flex">
      <AppNav />
      <main className="ml-64 flex-1 flex overflow-hidden h-screen">
        {/* Conversations sidebar */}
        <div className="w-80 border-r border-border flex flex-col bg-card/30">
          <div className="p-4 border-b border-border space-y-3">
            <h2 className="font-bold text-foreground">Messages</h2>
            <div className="relative">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <Input placeholder="Search conversations..." className="pl-9 bg-card border-border h-9 text-sm" />
            </div>
          </div>
          <div className="flex-1 overflow-y-auto">
            {conversations.map((conv) => (
              <button
                key={conv.id}
                onClick={() => setActive(conv)}
                className={`w-full flex items-center gap-3 p-4 hover:bg-accent transition-colors border-b border-border/50 ${active.id === conv.id ? "bg-primary/10 border-l-2 border-l-primary" : ""}`}
              >
                <div className="relative flex-shrink-0">
                  <div className="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style={{ background: conv.color }}>
                    {conv.name[0]}
                  </div>
                  {conv.online && <div className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-background" />}
                </div>
                <div className="flex-1 min-w-0 text-left">
                  <div className="flex items-center justify-between">
                    <span className="font-medium text-sm text-foreground">{conv.name}, {conv.age}</span>
                    <span className="text-xs text-muted-foreground">{conv.time}</span>
                  </div>
                  <p className="text-xs text-muted-foreground truncate mt-0.5">{conv.lastMsg}</p>
                </div>
                {conv.unread > 0 && (
                  <Badge className="bg-primary text-primary-foreground text-xs h-5 w-5 p-0 flex items-center justify-center rounded-full flex-shrink-0">
                    {conv.unread}
                  </Badge>
                )}
              </button>
            ))}
          </div>
        </div>

        {/* Chat area */}
        <div className="flex-1 flex flex-col">
          {/* Chat header */}
          <div className="h-16 border-b border-border flex items-center justify-between px-6 bg-background/80 backdrop-blur flex-shrink-0">
            <div className="flex items-center gap-3">
              <div className="relative">
                <div className="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm" style={{ background: active.color }}>
                  {active.name[0]}
                </div>
                {active.online && <div className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border border-background" />}
              </div>
              <div>
                <p className="font-semibold text-foreground text-sm">{active.name}, {active.age}</p>
                <p className="text-xs text-muted-foreground">{active.online ? "Active now" : "Last seen 2h ago"}</p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
                <Phone className="w-4 h-4" />
              </Button>
              <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
                <Video className="w-4 h-4" />
              </Button>
              <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
                <MapPin className="w-4 h-4" />
              </Button>
              <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
                <MoreVertical className="w-4 h-4" />
              </Button>
            </div>
          </div>

          {/* Messages */}
          <div className="flex-1 overflow-y-auto p-6 space-y-4">
            {chatMessages.map((msg) => (
              <div key={msg.id} className={`flex ${msg.from === "me" ? "justify-end" : "justify-start"}`}>
                {msg.from === "them" && (
                  <div className="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2 flex-shrink-0 mt-auto" style={{ background: active.color }}>
                    {active.name[0]}
                  </div>
                )}
                <div className={`max-w-xs lg:max-w-md`}>
                  <div className={`px-4 py-2.5 rounded-2xl text-sm leading-relaxed ${msg.from === "me" ? "bg-primary text-primary-foreground rounded-br-sm" : "bg-card border border-border text-foreground rounded-bl-sm"}`}>
                    {msg.text}
                  </div>
                  <p className="text-xs text-muted-foreground mt-1 px-1">{msg.time}</p>
                </div>
              </div>
            ))}
          </div>

          {/* Input area */}
          <div className="border-t border-border p-4 flex-shrink-0">
            <div className="flex items-center gap-3">
              <Input
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                placeholder="Type a message..."
                className="flex-1 bg-card border-border h-11"
                onKeyDown={(e) => { if (e.key === "Enter") setMessage(""); }}
              />
              <Button
                onClick={() => setMessage("")}
                className="bg-primary hover:bg-primary/90 text-primary-foreground h-11 w-11 p-0 rounded-xl"
              >
                <Send className="w-4 h-4" />
              </Button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
