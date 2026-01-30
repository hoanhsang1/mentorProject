from django import forms
from .models import User
import bcrypt

class LoginForm(forms.Form):
    username = forms.CharField(label='Email hoặc Tên đăng nhập', max_length=150)
    password = forms.CharField(label='Mật khẩu', widget=forms.PasswordInput)

class RegisterForm(forms.Form):
    username = forms.CharField(label='Tên đăng nhập', max_length=150)
    fullname = forms.CharField(label='Họ và tên', max_length=150)
    email = forms.EmailField(label='Email')
    password = forms.CharField(label='Mật khẩu', widget=forms.PasswordInput)
    confirm_password = forms.CharField(label='Xác nhận mật khẩu', widget=forms.PasswordInput)
    agree_terms = forms.BooleanField(label='Đồng ý điều khoản', required=True)

    def clean_username(self):
        username = self.cleaned_data['username']
        if User.objects.filter(username=username).exists():
            raise forms.ValidationError('Tên đăng nhập đã tồn tại.')
        return username

    def clean_email(self):
        email = self.cleaned_data['email']
        if User.objects.filter(email=email).exists():
            raise forms.ValidationError('Email đã tồn tại.')
        return email

    def clean(self):
        cleaned_data = super().clean()
        password = cleaned_data.get("password")
        confirm_password = cleaned_data.get("confirm_password")

        if password and confirm_password and password != confirm_password:
            self.add_error('confirm_password', "Mật khẩu xác nhận không khớp.")
        
        return cleaned_data
