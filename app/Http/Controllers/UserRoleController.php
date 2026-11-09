<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
class UserRoleController extends Controller {
 public function index(){ $users=User::orderBy('name')->paginate(20);$workers=Worker::orderBy('name')->get();return view('operations.users',compact('users','workers')); }
 public function update(Request $r,User $user){$d=$r->validate(['role'=>['required','in:superadmin,project_manager,logistics,finance,field_staff'],'worker_id'=>['nullable','required_if:role,field_staff','exists:workers,id']]);if($d['role']!=='field_staff')$d['worker_id']=null;abort_if($r->user()->is($user)&&$d['role']!=='superadmin',422,'Tidak dapat menurunkan role akun sendiri.');if($user->role==='superadmin'&&$d['role']!=='superadmin')abort_if(User::where('role','superadmin')->count()<=1,422,'Minimal harus ada satu akun Superadmin.');$user->update($d);return back()->with('success','Role pengguna diperbarui.');}
}