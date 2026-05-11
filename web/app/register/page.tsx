import Link from "next/link";
import { Heart } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

export default function RegisterPage() {
  return (
    <div className="min-h-screen bg-background flex items-center justify-center p-4">
      <div className="w-full max-w-md space-y-6">
        <div className="text-center">
          <Link href="/" className="inline-flex items-center gap-2 mb-6">
            <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
              <Heart className="w-5 h-5 text-primary-foreground" />
            </div>
            <span className="text-2xl font-bold text-foreground">pH7Builder</span>
          </Link>
          <h1 className="text-3xl font-bold text-foreground">Create your account</h1>
          <p className="text-muted-foreground mt-2">Join 5M+ singles finding real connections</p>
        </div>
        <Card className="bg-card border-border">
          <CardHeader className="pb-4">
            <CardTitle className="text-foreground">Get Started Free</CardTitle>
            <CardDescription>No credit card required</CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-2 gap-3">
              <div className="space-y-2">
                <Label htmlFor="first-name">First Name</Label>
                <Input id="first-name" placeholder="Alex" className="bg-input border-border" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="last-name">Last Name</Label>
                <Input id="last-name" placeholder="Johnson" className="bg-input border-border" />
              </div>
            </div>
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                placeholder="you@example.com"
                className="bg-input border-border"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <Input
                id="password"
                type="password"
                placeholder="At least 8 characters"
                className="bg-input border-border"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="dob">Date of Birth</Label>
              <Input id="dob" type="date" className="bg-input border-border" />
            </div>
            <div className="space-y-2">
              <Label>I am looking for</Label>
              <div className="grid grid-cols-3 gap-2">
                {["Women", "Men", "Everyone"].map((option) => (
                  <button
                    key={option}
                    className="px-3 py-2 rounded-lg border border-border text-sm text-muted-foreground hover:border-primary hover:text-primary transition-all"
                  >
                    {option}
                  </button>
                ))}
              </div>
            </div>
            <Button className="w-full bg-primary hover:bg-primary/90 text-primary-foreground font-semibold">
              Create Account
            </Button>
            <p className="text-xs text-muted-foreground text-center">
              By creating an account you agree to our{" "}
              <Link href="/terms" className="text-primary hover:underline">Terms</Link> and{" "}
              <Link href="/privacy" className="text-primary hover:underline">Privacy Policy</Link>
            </p>
          </CardContent>
        </Card>
        <p className="text-center text-sm text-muted-foreground">
          Already have an account?{" "}
          <Link href="/login" className="text-primary hover:underline font-medium">
            Sign in
          </Link>
        </p>
      </div>
    </div>
  );
}
