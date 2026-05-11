import { AppNav } from "@/components/Navbar";
import { Search, MessageCircle, MapPin } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import Link from "next/link";

const newMatches = [
  { name: "Elena", age: 24, color: "#7c3aed", online: true, match: 94 },
  { name: "Sophia", age: 26, color: "#db2777", online: false, match: 87 },
  { name: "Maya", age: 23, color: "#059669", online: true, match: 91 },
  { name: "Rachel", age: 28, color: "#d97706", online: true, match: 79 },
  { name: "Jessica", age: 25, color: "#2563eb", online: false, match: 85 },
];

const allMatches = [
  { name: "Elena", age: 24, job: "UX Designer", location: "2 mi", color: "#7c3aed", online: true, match: 94, lastMsg: "Hey! I saw you like hiking too 🏔️", time: "2m" },
  { name: "Sophia", age: 26, job: "Marketing Lead", location: "4 mi", color: "#db2777", online: false, match: 87, lastMsg: "Would love to grab coffee sometime!", time: "1h" },
  { name: "Maya", age: 23, job: "Software Engineer", location: "1 mi", color: "#059669", online: true, match: 91, lastMsg: "What kind of music are you into?", time: "3h" },
  { name: "Rachel", age: 28, job: "Therapist", location: "6 mi", color: "#d97706", online: true, match: 79, lastMsg: "Haha that's so funny 😂", time: "1d" },
  { name: "Jessica", age: 25, job: "Photographer", location: "3 mi", color: "#2563eb", online: false, match: 85, lastMsg: "We should check out that gallery", time: "2d" },
];

export default function MatchesPage() {
  return (
    <div className="min-h-screen bg-background flex">
      <AppNav />
      <main className="ml-64 flex-1">
        <div className="max-w-3xl mx-auto px-6 py-8 space-y-8">
          {/* Header */}
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl font-bold text-foreground">Your Matches</h1>
              <p className="text-muted-foreground text-sm mt-1">You have {allMatches.length} matches</p>
            </div>
            <div className="relative w-64">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <Input placeholder="Search matches..." className="pl-9 bg-card border-border h-9 text-sm" />
            </div>
          </div>

          {/* New matches row */}
          <div className="bg-card border border-border rounded-2xl p-6 space-y-4">
            <div className="flex items-center justify-between">
              <h2 className="font-semibold text-foreground">New Matches</h2>
              <Badge className="bg-primary/10 text-primary border-primary/20">{newMatches.length} new</Badge>
            </div>
            <div className="flex gap-4 overflow-x-auto pb-2">
              {newMatches.map((m) => (
                <div key={m.name} className="flex flex-col items-center gap-2 flex-shrink-0 cursor-pointer group">
                  <div className="relative">
                    <div className="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl ring-2 ring-transparent group-hover:ring-primary transition-all" style={{ background: m.color }}>
                      {m.name[0]}
                    </div>
                    {m.online && <div className="absolute bottom-0.5 right-0.5 w-4 h-4 bg-green-500 rounded-full border-2 border-card" />}
                  </div>
                  <span className="text-sm text-muted-foreground group-hover:text-foreground transition-colors">{m.name}</span>
                  <Badge variant="secondary" className="text-xs px-2 py-0.5">{m.match}%</Badge>
                </div>
              ))}
            </div>
          </div>

          {/* All matches list */}
          <div className="space-y-3">
            <h2 className="font-semibold text-foreground">All Matches</h2>
            {allMatches.map((m) => (
              <div key={m.name} className="bg-card border border-border rounded-2xl p-4 flex items-center gap-4 hover:border-primary/30 transition-all cursor-pointer group">
                <div className="relative flex-shrink-0">
                  <div className="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl" style={{ background: m.color }}>
                    {m.name[0]}
                  </div>
                  {m.online && <div className="absolute bottom-0.5 right-0.5 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-card" />}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <span className="font-semibold text-foreground">{m.name}, {m.age}</span>
                    <Badge variant="secondary" className="text-xs">{m.match}% match</Badge>
                  </div>
                  <div className="flex items-center gap-3 text-xs text-muted-foreground mt-0.5">
                    <span>{m.job}</span>
                    <span className="flex items-center gap-0.5"><MapPin className="w-3 h-3" />{m.location}</span>
                  </div>
                  <p className="text-sm text-muted-foreground truncate mt-1">{m.lastMsg}</p>
                </div>
                <div className="flex flex-col items-end gap-2 flex-shrink-0">
                  <span className="text-xs text-muted-foreground">{m.time}</span>
                  <Link href="/messages">
                    <Button size="sm" variant="ghost" className="h-8 w-8 p-0 hover:bg-primary/10 hover:text-primary">
                      <MessageCircle className="w-4 h-4" />
                    </Button>
                  </Link>
                </div>
              </div>
            ))}
          </div>
        </div>
      </main>
    </div>
  );
}
