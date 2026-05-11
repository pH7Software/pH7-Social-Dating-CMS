import React, { useState } from 'react';
import { 
  Heart, X, Star, MessageCircle, User, Settings, 
  Search, Bell, MapPin, Briefcase, GraduationCap,
  Flame, LayoutDashboard
} from 'lucide-react';

const PROFILES = [
  {
    id: 1,
    name: "Elena",
    age: 24,
    location: "New York, NY",
    distance: "2 miles away",
    bio: "Coffee addict, amateur photographer, and professional dog petter. Looking for someone to explore the city with.",
    job: "UX Designer",
    school: "NYU",
    image: "/__mockup/images/profile-1.png",
    matchPercentage: 94
  },
  {
    id: 2,
    name: "Marcus",
    age: 27,
    location: "Brooklyn, NY",
    distance: "4 miles away",
    bio: "Just moved to the city. Love hiking, indie music, and finding the best pizza spots.",
    job: "Software Engineer",
    school: "Columbia University",
    image: "/__mockup/images/profile-2.png",
    matchPercentage: 88
  },
  {
    id: 3,
    name: "Sarah",
    age: 26,
    location: "Manhattan, NY",
    distance: "1 mile away",
    bio: "Always planning my next trip. Tell me about your favorite travel destination!",
    job: "Marketing Manager",
    school: "Boston University",
    image: "/__mockup/images/profile-3.png",
    matchPercentage: 91
  }
];

const RECENT_MATCHES = [
  { id: 101, name: "Jessica", image: "/__mockup/images/profile-3.png", online: true },
  { id: 102, name: "David", image: "/__mockup/images/profile-2.png", online: false },
  { id: 103, name: "Amanda", image: "/__mockup/images/profile-1.png", online: true },
  { id: 104, name: "Chris", image: "/__mockup/images/profile-2.png", online: false },
];

const NOTIFICATIONS = [
  { id: 1, text: "Jessica sent you a message", time: "2m ago", unread: true },
  { id: 2, text: "You have a new match!", time: "1h ago", unread: false },
  { id: 3, text: "Someone liked your profile", time: "3h ago", unread: false },
];

export default function Dashboard() {
  const [currentProfileIndex, setCurrentProfileIndex] = useState(0);
  const [direction, setDirection] = useState<"left" | "right" | null>(null);

  const currentProfile = PROFILES[currentProfileIndex];

  const handleAction = (action: "like" | "skip" | "superlike") => {
    if (action === "like") setDirection("right");
    if (action === "skip") setDirection("left");
    
    setTimeout(() => {
      setDirection(null);
      setCurrentProfileIndex((prev) => (prev + 1) % PROFILES.length);
    }, 300);
  };

  return (
    <div className="flex h-screen w-full bg-[#0a0a0f] text-white font-sans overflow-hidden selection:bg-pink-500 selection:text-white">
      
      {/* Left Sidebar */}
      <aside className="w-64 border-r border-white/10 bg-[#0f0f14]/80 backdrop-blur-xl flex flex-col justify-between hidden md:flex">
        <div>
          <div className="p-6 flex items-center gap-3">
            <div className="w-8 h-8 rounded-full bg-gradient-to-tr from-pink-500 to-purple-600 flex items-center justify-center">
              <Heart className="w-4 h-4 text-white fill-white" />
            </div>
            <span className="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-500">
              Aura
            </span>
          </div>

          <nav className="px-4 mt-6 space-y-1">
            <NavItem icon={<LayoutDashboard />} label="Discover" active />
            <NavItem icon={<Heart />} label="Matches" badge="4" />
            <NavItem icon={<MessageCircle />} label="Messages" badge="2" />
            <NavItem icon={<User />} label="Profile" />
            <NavItem icon={<Settings />} label="Settings" />
          </nav>
        </div>

        <div className="p-4 m-4 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3 hover:bg-white/10 transition-colors cursor-pointer">
          <img 
            src="/__mockup/images/profile-1.png" 
            alt="My Profile" 
            className="w-10 h-10 rounded-full object-cover border-2 border-purple-500"
          />
          <div className="flex-1 min-w-0">
            <h4 className="text-sm font-semibold text-white truncate">Alex, 25</h4>
            <p className="text-xs text-white/50 truncate">Premium Member</p>
          </div>
        </div>
      </aside>

      {/* Main Content Area */}
      <main className="flex-1 flex flex-col relative overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-purple-900/20 via-[#0a0a0f] to-[#0a0a0f]">
        
        {/* Top Header */}
        <header className="h-20 border-b border-white/10 bg-[#0f0f14]/50 backdrop-blur-md flex items-center justify-between px-8 z-10">
          <div className="flex-1 max-w-md relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40" />
            <input 
              type="text" 
              placeholder="Search matches or messages..." 
              className="w-full bg-white/5 border border-white/10 rounded-full py-2 pl-10 pr-4 text-sm focus:outline-none focus:border-pink-500/50 focus:ring-1 focus:ring-pink-500/50 transition-all text-white placeholder:text-white/40"
            />
          </div>

          <div className="flex items-center gap-4 ml-4">
            <button className="relative p-2 text-white/60 hover:text-white transition-colors">
              <Bell className="w-5 h-5" />
              <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-pink-500 rounded-full border border-[#0f0f14]"></span>
            </button>
            <button className="flex items-center gap-2 bg-gradient-to-r from-pink-500 to-purple-600 px-4 py-2 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">
              <Flame className="w-4 h-4" />
              <span>Boost</span>
            </button>
          </div>
        </header>

        {/* Discovery Feed */}
        <div className="flex-1 flex items-center justify-center p-6 relative">
          
          {/* Background blurred card for stack effect */}
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm h-[600px] bg-white/5 rounded-3xl border border-white/5 scale-95 opacity-50 translate-y-4 -z-10 blur-sm pointer-events-none"></div>

          {currentProfile ? (
            <div 
              className={`w-full max-w-sm h-[600px] bg-[#1a1a24] rounded-3xl shadow-2xl border border-white/10 overflow-hidden flex flex-col relative transition-transform duration-300 ease-in-out ${
                direction === 'left' ? '-translate-x-[150%] rotate-[-15deg] opacity-0' : 
                direction === 'right' ? 'translate-x-[150%] rotate-[15deg] opacity-0' : 'translate-x-0 rotate-0 opacity-100'
              }`}
            >
              {/* Profile Image with Gradient overlay */}
              <div className="relative h-3/5 w-full shrink-0 bg-zinc-800">
                <img 
                  src={currentProfile.image} 
                  alt={currentProfile.name} 
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#1a1a24] via-[#1a1a24]/20 to-transparent"></div>
                
                {/* Match Badge */}
                <div className="absolute top-4 left-4 bg-black/40 backdrop-blur-md border border-white/10 px-3 py-1 rounded-full flex items-center gap-1.5">
                  <Star className="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" />
                  <span className="text-xs font-semibold">{currentProfile.matchPercentage}% Match</span>
                </div>
              </div>

              {/* Profile Details */}
              <div className="p-6 flex-1 flex flex-col overflow-y-auto no-scrollbar relative z-10 -mt-10">
                <div className="flex items-end justify-between mb-4">
                  <div>
                    <h2 className="text-3xl font-bold text-white flex items-center gap-2">
                      {currentProfile.name} <span className="text-white/60 font-medium text-2xl">{currentProfile.age}</span>
                    </h2>
                    <p className="text-pink-400 font-medium text-sm mt-1">{currentProfile.job}</p>
                  </div>
                </div>

                <div className="space-y-3 mb-6">
                  <div className="flex items-center gap-2 text-white/70 text-sm">
                    <MapPin className="w-4 h-4 opacity-50" />
                    <span>{currentProfile.location} • {currentProfile.distance}</span>
                  </div>
                  <div className="flex items-center gap-2 text-white/70 text-sm">
                    <GraduationCap className="w-4 h-4 opacity-50" />
                    <span>{currentProfile.school}</span>
                  </div>
                </div>

                <div className="mb-6">
                  <h3 className="text-xs font-semibold text-white/40 uppercase tracking-wider mb-2">About Me</h3>
                  <p className="text-white/80 text-sm leading-relaxed">
                    {currentProfile.bio}
                  </p>
                </div>

                <div className="mt-auto">
                  <div className="flex items-center justify-center gap-6">
                    <button 
                      onClick={() => handleAction('skip')}
                      className="w-14 h-14 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 hover:scale-110 hover:border-red-500/50 transition-all group"
                    >
                      <X className="w-6 h-6 group-hover:text-red-400" />
                    </button>
                    
                    <button 
                      onClick={() => handleAction('superlike')}
                      className="w-12 h-12 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center text-blue-400 hover:bg-blue-500/30 hover:scale-110 transition-all group shadow-[0_0_20px_rgba(59,130,246,0.3)]"
                    >
                      <Star className="w-5 h-5 group-hover:fill-blue-400" />
                    </button>

                    <button 
                      onClick={() => handleAction('like')}
                      className="w-16 h-16 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 flex items-center justify-center text-white hover:scale-110 hover:shadow-[0_0_30px_rgba(236,72,153,0.5)] transition-all group"
                    >
                      <Heart className="w-8 h-8 group-hover:fill-white" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          ) : (
            <div className="flex flex-col items-center justify-center text-center space-y-4 opacity-60">
              <div className="w-20 h-20 rounded-full border-2 border-dashed border-white/20 flex items-center justify-center">
                <Search className="w-8 h-8 text-white/40" />
              </div>
              <h3 className="text-xl font-bold">No more profiles</h3>
              <p className="text-sm text-white/50 max-w-xs">You've seen everyone in your area. Try expanding your search radius.</p>
              <button 
                onClick={() => setCurrentProfileIndex(0)}
                className="mt-4 px-6 py-2 bg-white/10 rounded-full text-sm font-medium hover:bg-white/20 transition-colors"
              >
                Reset Stack
              </button>
            </div>
          )}

        </div>
      </main>

      {/* Right Sidebar */}
      <aside className="w-80 border-l border-white/10 bg-[#0f0f14]/80 backdrop-blur-xl flex flex-col hidden lg:flex">
        
        {/* Matches Grid */}
        <div className="p-6 border-b border-white/10">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-sm font-bold text-white uppercase tracking-wider">New Matches</h3>
            <button className="text-xs text-pink-400 hover:text-pink-300">See all</button>
          </div>
          
          <div className="grid grid-cols-4 gap-3">
            {RECENT_MATCHES.map((match) => (
              <div key={match.id} className="flex flex-col items-center gap-2 cursor-pointer group">
                <div className="relative">
                  <img 
                    src={match.image} 
                    alt={match.name} 
                    className="w-14 h-14 rounded-full object-cover border-2 border-transparent group-hover:border-pink-500 transition-colors"
                  />
                  {match.online && (
                    <div className="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-[#0f0f14] rounded-full"></div>
                  )}
                </div>
                <span className="text-xs font-medium text-white/80 group-hover:text-white truncate w-full text-center">{match.name}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Notifications */}
        <div className="flex-1 overflow-y-auto p-6">
          <h3 className="text-sm font-bold text-white uppercase tracking-wider mb-4">Activity</h3>
          
          <div className="space-y-4">
            {NOTIFICATIONS.map((notif) => (
              <div key={notif.id} className="flex gap-3 items-start p-3 rounded-xl hover:bg-white/5 transition-colors cursor-pointer group">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center shrink-0 ${notif.unread ? 'bg-pink-500/20 text-pink-400' : 'bg-white/5 text-white/40'}`}>
                  {notif.id === 1 ? <MessageCircle className="w-4 h-4" /> : 
                   notif.id === 2 ? <Star className="w-4 h-4" /> : 
                   <Heart className="w-4 h-4" />}
                </div>
                <div>
                  <p className={`text-sm ${notif.unread ? 'text-white font-medium' : 'text-white/70'}`}>
                    {notif.text}
                  </p>
                  <span className="text-xs text-white/40 mt-1 block">{notif.time}</span>
                </div>
              </div>
            ))}
          </div>
          
          <div className="mt-8 p-5 rounded-2xl bg-gradient-to-br from-pink-500/10 to-purple-600/10 border border-pink-500/20 relative overflow-hidden group cursor-pointer">
            <div className="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-pink-500/20 blur-2xl rounded-full group-hover:bg-pink-500/30 transition-all"></div>
            <Flame className="w-6 h-6 text-pink-400 mb-2" />
            <h4 className="text-sm font-bold text-white mb-1">Get more matches!</h4>
            <p className="text-xs text-white/60 mb-3">Upgrade to Premium to see who liked you.</p>
            <button className="text-xs font-bold text-pink-400 hover:text-pink-300">Upgrade Now &rarr;</button>
          </div>
        </div>

      </aside>
    </div>
  );
}

function NavItem({ icon, label, active, badge }: { icon: React.ReactNode, label: string, active?: boolean, badge?: string }) {
  return (
    <a 
      href="#" 
      className={`flex items-center justify-between px-3 py-3 rounded-xl transition-all ${
        active 
          ? 'bg-gradient-to-r from-pink-500/10 to-purple-600/10 text-pink-400 font-medium' 
          : 'text-white/60 hover:text-white hover:bg-white/5'
      }`}
    >
      <div className="flex items-center gap-3">
        {React.cloneElement(icon as React.ReactElement, { className: 'w-5 h-5' })}
        <span className="text-sm">{label}</span>
      </div>
      {badge && (
        <span className="bg-pink-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
          {badge}
        </span>
      )}
    </a>
  );
}
