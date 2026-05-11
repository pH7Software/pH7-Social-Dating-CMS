import React from "react";
import { X, Star, Heart, MapPin, CheckCircle2, ChevronRight } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

export function Profile() {
  return (
    <div className="min-h-screen bg-slate-50 flex justify-center font-sans pb-24">
      <div className="w-full max-w-md bg-white shadow-xl overflow-hidden relative shadow-indigo-100/50">
        
        {/* Cover Photo / Header */}
        <div className="h-64 bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 relative">
          <div className="absolute inset-0 opacity-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
          
          <Button variant="ghost" size="icon" className="absolute top-4 left-4 text-white hover:bg-white/20 rounded-full">
            <ChevronRight className="h-6 w-6 rotate-180" />
          </Button>

          <Button variant="ghost" size="icon" className="absolute top-4 right-4 text-white hover:bg-white/20 rounded-full">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 13C12.5523 13 13 12.5523 13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinelinejoin="round"/>
              <path d="M12 6C12.5523 6 13 5.55228 13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinelinejoin="round"/>
              <path d="M12 20C12.5523 20 13 19.5523 13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20Z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinelinejoin="round"/>
            </svg>
          </Button>
        </div>

        {/* Profile Info Overlay */}
        <div className="px-6 relative -mt-20">
          <div className="flex justify-between items-end mb-4">
            <div className="relative">
              <div className="w-32 h-32 rounded-full border-4 border-white overflow-hidden bg-gray-100 shadow-lg relative">
                <img 
                  src="/__mockup/images/user-main.png" 
                  alt="Profile" 
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="absolute bottom-2 right-2 w-5 h-5 bg-green-500 border-2 border-white rounded-full shadow-sm z-10"></div>
            </div>
            
            {/* Compatibility Score */}
            <div className="flex flex-col items-center mb-2">
              <div className="relative w-16 h-16 flex items-center justify-center">
                <svg className="w-full h-full" viewBox="0 0 36 36">
                  <path
                    className="text-gray-200"
                    strokeWidth="3"
                    stroke="currentColor"
                    fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                  <path
                    className="text-purple-600"
                    strokeWidth="3"
                    strokeDasharray="87, 100"
                    strokeLinecap="round"
                    stroke="currentColor"
                    fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                  />
                </svg>
                <div className="absolute flex items-center justify-center text-sm font-bold text-indigo-950">
                  87%
                </div>
              </div>
              <span className="text-[10px] uppercase font-bold tracking-wider text-gray-500 mt-1">Match</span>
            </div>
          </div>

          <div className="mb-6">
            <h1 className="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
              Elena, 26
              <CheckCircle2 className="w-6 h-6 text-blue-500 fill-blue-50" />
            </h1>
            <div className="flex items-center text-gray-500 mt-1 font-medium">
              <MapPin className="w-4 h-4 mr-1 text-gray-400" />
              San Francisco, CA • 2 miles away
            </div>
          </div>

          {/* Stats Bar */}
          <div className="flex justify-between items-center bg-gray-50 rounded-2xl p-4 mb-8 shadow-sm border border-gray-100">
            <div className="text-center flex-1">
              <div className="text-xl font-bold text-gray-900">142</div>
              <div className="text-xs text-gray-500 font-medium uppercase tracking-wide">Matches</div>
            </div>
            <div className="w-px h-8 bg-gray-200"></div>
            <div className="text-center flex-1">
              <div className="text-xl font-bold text-gray-900">89</div>
              <div className="text-xs text-gray-500 font-medium uppercase tracking-wide">Likes</div>
            </div>
            <div className="w-px h-8 bg-gray-200"></div>
            <div className="text-center flex-1">
              <div className="text-xl font-bold text-gray-900 flex items-center justify-center gap-1">
                4.9 <Star className="w-4 h-4 text-amber-400 fill-amber-400" />
              </div>
              <div className="text-xs text-gray-500 font-medium uppercase tracking-wide">Rating</div>
            </div>
          </div>

          {/* About */}
          <div className="mb-8">
            <h2 className="text-lg font-bold text-gray-900 mb-3">About Me</h2>
            <p className="text-gray-600 leading-relaxed">
              Software engineer by day, amateur chef by night. I love exploring the city for the best hidden coffee spots and taking weekend hiking trips to clear my mind. Looking for someone who can match my energy and isn't afraid of a little friendly competition in Mario Kart. ☕️🌿🏔️
            </p>
          </div>

          {/* Interests */}
          <div className="mb-8">
            <h2 className="text-lg font-bold text-gray-900 mb-3">Interests</h2>
            <div className="flex flex-wrap gap-2">
              {['Travel', 'Cooking', 'Photography', 'Hiking', 'Live Music', 'Coffee', 'Dogs'].map(interest => (
                <Badge key={interest} variant="secondary" className="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-sm font-medium border-0">
                  {interest}
                </Badge>
              ))}
            </div>
          </div>

          {/* Photos */}
          <div className="mb-8">
            <div className="flex justify-between items-center mb-3">
              <h2 className="text-lg font-bold text-gray-900">Photos</h2>
              <Button variant="link" className="text-indigo-600 font-semibold p-0 h-auto">View All</Button>
            </div>
            <div className="grid grid-cols-3 gap-2">
              {[1, 2, 3, 4, 5, 6].map(i => (
                <div key={i} className="aspect-[4/5] rounded-xl overflow-hidden bg-gray-100 shadow-sm relative group cursor-pointer">
                  <img 
                    src={`/__mockup/images/gallery-${i}.png`} 
                    alt={`Gallery ${i}`} 
                    className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                  />
                  <div className="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
              ))}
            </div>
          </div>

          {/* Basic Info Details */}
          <div className="mb-10 space-y-4">
            <h2 className="text-lg font-bold text-gray-900 mb-3">Basics</h2>
            <div className="bg-gray-50 rounded-2xl p-5 space-y-4 border border-gray-100">
              <div className="flex items-center text-gray-700">
                <span className="w-8 flex justify-center text-xl">🎓</span>
                <span className="ml-3 font-medium">Stanford University</span>
              </div>
              <div className="flex items-center text-gray-700">
                <span className="w-8 flex justify-center text-xl">💼</span>
                <span className="ml-3 font-medium">Software Engineer at Tech Co.</span>
              </div>
              <div className="flex items-center text-gray-700">
                <span className="w-8 flex justify-center text-xl">📏</span>
                <span className="ml-3 font-medium">5'7" (170cm)</span>
              </div>
              <div className="flex items-center text-gray-700">
                <span className="w-8 flex justify-center text-xl">🍷</span>
                <span className="ml-3 font-medium">Socially</span>
              </div>
              <div className="flex items-center text-gray-700">
                <span className="w-8 flex justify-center text-xl">🏃‍♀️</span>
                <span className="ml-3 font-medium">Active</span>
              </div>
            </div>
          </div>

        </div>

        {/* Action Bar (Sticky) */}
        <div className="fixed bottom-0 w-full max-w-md bg-gradient-to-t from-white via-white to-transparent pt-12 pb-6 px-6 z-20">
          <div className="flex justify-center items-center gap-6">
            <button className="w-14 h-14 rounded-full bg-white shadow-[0_4px_15px_rgba(0,0,0,0.1)] flex items-center justify-center text-rose-500 hover:scale-110 hover:bg-rose-50 transition-all active:scale-95 border border-gray-100">
              <X className="w-6 h-6 stroke-[3]" />
            </button>
            <button className="w-12 h-12 rounded-full bg-white shadow-[0_4px_15px_rgba(0,0,0,0.1)] flex items-center justify-center text-sky-500 hover:scale-110 hover:bg-sky-50 transition-all active:scale-95 border border-gray-100">
              <Star className="w-5 h-5 fill-sky-500" />
            </button>
            <button className="w-16 h-16 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-500 shadow-[0_8px_20px_rgba(99,102,241,0.4)] flex items-center justify-center text-white hover:scale-110 transition-all active:scale-95">
              <Heart className="w-8 h-8 fill-white" />
            </button>
          </div>
        </div>

      </div>
    </div>
  );
}

export default Profile;
